<template>
    <div>
        <h1>Hi {{user.name}}!</h1>

        <div class="content">
            <div class="demo">
                <div class="card-scene">
                    <div class="form-inline">
                        <input type="text" v-model="newColumnName" class="form-control mb-2 mr-sm-2" placeholder="Column's name"/>
                        <span class="btn btn-outline-info mb-2" @click="addColumn(newColumnName)">&#x2b; Add Column</span>
                    </div>
                    <Container
                        orientation="horizontal"
                        @drop="onColumnDrop($event)"
                        drag-handle-selector=".column-drag-handle"
                        @drag-start="dragStart"
                        :drop-placeholder="upperDropPlaceholderOptions"
                    >
                        <Draggable v-for="column in columns" :key="column.id">
                            <div :class="'card-container'">
                                <span class="btn mr-3" :class="[ is_edit_column == column.id ? 'btn-info' : 'btn-outline-info' ]" @click="editColumn(column.id)">&#x270E; Edit column</span>
                                <span class="btn btn-outline-info" @click="addCard(column.id)">&#x2b; Add card</span>
                                <span class="btn btn-outline-danger mt-3" @click="removeColumn(column.id)">&#x2715; Remove column</span>
                                <div class="card-column-header">
                                    <span class="column-drag-handle">&#x2630;</span>
                                    <input type="text" v-model="column.name" :disabled="is_edit_column != column.id"/>
                                </div>
                                <Container
                                    group-name="col"
                                    @drop="(e) => onCardDrop(column.id, e)"
                                    @drag-start="(e) => log('drag start', e)"
                                    @drag-end="(e) => log('drag end', e)"
                                    :get-child-payload="getCardPayload(column.id)"
                                    drag-class="card-ghost"
                                    drop-class="card-ghost-drop"
                                    :drop-placeholder="dropPlaceholderOptions"
                                >
                                    <Draggable v-for="card in cards" :key="card.id">
                                        <div v-if="card.column_id === column.id" :class="'card'" :style="{ backgroundColor: 'bisque' }">
                                            <span class="btn" :class="[ is_edit_card == card.id ? 'btn-info' : 'btn-outline-info' ]" @click="editCard(card.id)">&#x270E; Edit card</span>
                                            <span class="btn btn-outline-danger mt-3" @click="removeCard(card.id)">&#x2715; Remove card</span>
                                            <input type="text" v-model="card.name" v-show="is_edit_card == card.id"/>
                                            <h1 v-if="is_edit_card != card.id">{{ card.name }}</h1>
                                            <p>{{ card.name }}</p>
                                        </div>
                                    </Draggable>
                                </Container>
                            </div>
                        </Draggable>
                    </Container>
                </div>
            </div>
        </div>

        <p>
            <router-link to="/login">Logout</router-link>
        </p>
    </div>
</template>

<script>
import { Container, Draggable } from 'vue-smooth-dnd'
import { applyDrag } from '../_helpers/drag_helpers'

export default {

    components: { Container, Draggable },

    data () {
        return {
            newColumnName: '',
            newCardName: '',
            is_edit_column: '',
            is_edit_card: '',

            upperDropPlaceholderOptions: {
                className: 'cards-drop-preview',
                animationDuration: '150',
                showOnTop: true
            },
            dropPlaceholderOptions: {
                className: 'drop-preview',
                animationDuration: '150',
                showOnTop: true
            },
            columns_copy: null,
            cards_copy: null,
        }
    },
    computed: {
        user() {
            return this.$store.state.user.user;
        },
        columns() {
            return this.$store.state.user.columns;
        },
        cards() {
            return this.$store.state.user.cards;
        },
    },
    created() {
        this.$store.dispatch('user/getUser');
        this.$store.dispatch('user/getColumns');
        this.$store.dispatch('user/getCards');
    },
    methods: {
        addColumn(newColumnName) {
            this.$store.dispatch('user/addColumn', newColumnName);
        },
        editColumn(column_id) {
            if (this.is_edit_column == column_id) {
                this.is_edit_column = ''
                this.$store.dispatch('user/editColumn', { name: this.columns.filter(column => column.id == column_id)[0].name, column_id });
            } else {
                this.is_edit_column = column_id    
            }
        },
        removeColumn(column_id) {
            this.$store.dispatch('user/removeColumn', column_id);
        },

        addCard(column_id) {
            this.$store.dispatch('user/addCard', column_id);
        },
        editCard(card_id) {
            if (this.is_edit_card == card_id) {
                this.is_edit_card = ''
                this.$store.dispatch('user/editCard', { name: this.cards.filter(card => card.id == card_id)[0].name, card_id });
            } else {
                this.is_edit_card = card_id
            }
        },
        removeCard(card_id) {
            this.$store.dispatch('user/removeCard', card_id);
        },

        onColumnDrop (dropResult) {
            let columns = this.columns
            columns = applyDrag(columns, dropResult)
            this.columns_copy = columns
            this.$store.dispatch('user/updateColumnsPositions', this.columns_copy);
        },
        onCardDrop (columnId, dropResult) {
            if (dropResult.removedIndex !== null || dropResult.addedIndex !== null) {
                const columns = this.columns
                const column = this.columns.filter(p => p.id === columnId)[0]
                const columnIndex = columns.indexOf(column)

                const newColumn = column
                
                let existingCards = this.cards.filter(c => c.column_id === newColumn.id)
                let newCards = applyDrag(this.cards.filter(c => c.column_id === newColumn.id), dropResult)

                if (!existingCards.equals(newCards)) {
                    let newCard;
                    newCards.forEach((card, idx) => {
                        if (newCards[idx][0]) {
                            newCards[idx][0].column_id = columnId
                            this.$store.dispatch('user/updateCardsPositions', newCards[idx][0]);
                        }
                    })
                } else {
                    existingCards.forEach(e_card => {
                        newCards = newCards.filter(card => card.id != e_card.id)
                    })
                }
            }
        },
        getCardPayload (columnId) {
            return _ => {
                return this.cards.filter(c => c.column_id === this.columns.filter(p => p.id === columnId)[0].id)
            }
        },
        dragStart () {
            console.log('drag started')
        },
        log (...params) {
            console.log(...params)
        },
    }
};

// Warn if overriding existing method
if(Array.prototype.equals)
    console.warn("Overriding existing Array.prototype.equals. Possible causes: New API defines the method, there's a framework conflict or you've got double inclusions in your code.");
// attach the .equals method to Array's prototype to call it on any array
Array.prototype.equals = function (array) {
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time 
    if (this.length != array.length)
        return false;

    for (var i = 0, l=this.length; i < l; i++) {
        // Check if we have nested arrays
        if (this[i] instanceof Array && array[i] instanceof Array) {
            // recurse into the nested arrays
            if (!this[i].equals(array[i]))
                return false;       
        }           
        else if (this[i] != array[i]) { 
            // Warning - two different object instances will never be equal: {x:20} != {x:20}
            return false;   
        }           
    }       
    return true;
}
// Hide method from for-in loops
Object.defineProperty(Array.prototype, "equals", {enumerable: false});
</script>
