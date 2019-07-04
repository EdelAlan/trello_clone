import { userService } from '../_services';

export const user = {
    namespaced: true,
    state: {
        user: {},
        columns: {},
        cards: {},
    },
    actions: {
        getUser({ commit }) {
            userService.getUser()
                .then(
                    user => commit('getUserSuccess', user),
                    error => commit('getUserFailure', error)
                );
        },

        addColumn({ commit }, newColumnName) {
            userService.addColumn(newColumnName)
                .then(
                    columns => commit('addColumnSuccess', columns),
                    error => commit('getColumnsFailure', error)
                );
        },
        getColumns({ commit }) {
            userService.getColumns()
                .then(
                    columns => commit('getColumnsSuccess', columns),
                    error => commit('getColumnsFailure', error)
                );
        },
        editColumn({ commit }, { name, column_id }) {
            userService.editColumn(name, column_id)
                .then(
                    res => commit('getColumnsSuccess', res.columns),
                    error => commit('getColumnsFailure', error)
                );
        },
        updateColumnsPositions({ commit }, columns) {
            userService.updateColumnsPositions(columns)
                .then(
                    columns => commit('getColumnsSuccess', columns),
                    error => commit('getColumnsFailure', error)
                );
        },
        removeColumn({ commit }, column_id) {
            userService.removeColumn(column_id)
                .then(
                    res => commit('getColumnsSuccess', res.columns),
                    error => commit('getColumnsFailure', error)
                );
        },

        addCard({ commit }, column_id) {
            userService.addCard(column_id)
                .then(
                    res => commit('addCardSuccess', res),
                    error => commit('getColumnsFailure', error)
                );
        },
        editCard({ commit }, { name, card_id }) {
            userService.editCard(name, card_id)
                .then(
                    res => commit('getCardsSuccess', res.cards),
                    error => commit('getCardsFailure', error)
                );
        },
        getCards({ commit }) {
            userService.getCards()
                .then(
                    cards => commit('getCardsSuccess', cards),
                    error => commit('getCardsFailure', error)
                );
        },
        updateCardsPositions({ commit }, card) {
            userService.updateCardsPositions(card)
                .then(
                    cards => commit('getCardsSuccess', cards),
                    error => commit('getCardsFailure', error)
                );
        },
        removeCard({ commit }, card_id) {
            userService.removeCard(card_id)
                .then(
                    res => commit('getCardsSuccess', res.cards),
                    error => commit('getColumnsFailure', error)
                );
        },
    },
    mutations: {
        getUserSuccess(state, user) {
            state.user = { ...user.user };
        },
        getUserFailure(state, error) {
            state.user = { error };
        },

        addColumnSuccess(state, res) {
            state.columns = res.columns;
        },
        getColumnsSuccess(state, columns) {
            console.log(columns)
            state.columns = columns;
        },
        getColumnsFailure(state, error) {
            state.columns = { error };
        },

        addCardSuccess(state, res) {
            state.cards = res.cards;
        },
        getCardsSuccess(state, cards) {
            console.log(cards)
            state.cards = cards;
        },
        getCardsFailure(state, error) {
            state.cards = { error };
        },

        getCardsSuccess(state, cards) {
            console.log(cards)
            state.cards = cards;
        },
        getCardsFailure(state, error) {
            state.cards = { error };
        },
    }
}
