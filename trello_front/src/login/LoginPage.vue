<template>
    <div class="jumbotron">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 offset-sm-3">
                    <nav>
                        <div class="nav nav-tabs">
                            <a class="nav-item nav-link active" data-toggle="tab" @click="is_login = true; $store.dispatch('alert/clear');">Login</a>
                            <a class="nav-item nav-link" data-toggle="tab" @click="is_login = false; $store.dispatch('alert/clear');">Register</a>
                        </div>
                    </nav>
                    <form v-if="is_login" @submit.prevent="login">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" v-model="email" name="email" class="form-control" :class="{ 'is-invalid': submitted && !email }" />
                            <div v-show="submitted && !email" class="invalid-feedback">Email is required</div>
                        </div>
                        <div class="form-group">
                            <label htmlFor="password">Password</label>
                            <input type="password" v-model="password" name="password" class="form-control" :class="{ 'is-invalid': submitted && !password }" />
                            <div v-show="submitted && !password" class="invalid-feedback">Password is required</div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" :disabled="loggingIn">Login</button>
                            <img v-show="loggingIn" src="data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==" />
                        </div>
                    </form>
                    <form v-if="!is_login" @submit.prevent="register">
                        <div class="form-group">
                            <label for="r_name">Name</label>
                            <input type="text" v-model="r_name" name="r_name" class="form-control" :class="{ 'is-invalid': r_submitted && !r_name }" />
                            <div v-show="r_submitted && !r_name" class="invalid-feedback">Name is required</div>
                        </div>
                        <div class="form-group">
                            <label for="r_email">Email</label>
                            <input type="text" v-model="r_email" name="r_email" class="form-control" :class="{ 'is-invalid': r_submitted && !r_email }" />
                            <div v-show="r_submitted && !r_email" class="invalid-feedback">Email is required</div>
                        </div>
                        <div class="form-group">
                            <label htmlFor="r_password">Password</label>
                            <input type="r_password" v-model="r_password" name="r_password" class="form-control" :class="{ 'is-invalid': r_submitted && !r_password }" />
                            <div v-show="r_submitted && !r_password" class="invalid-feedback">Password is required</div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" :disabled="registering">Register</button>
                            <img v-show="loggingIn" src="data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            is_login: true,

            email: '',
            password: '',
            submitted: false,

            r_name: '',
            r_email: '',
            r_password: '',
            r_submitted: false
        }
    },
    created() {
        this.$store.dispatch('authentication/logout');
    },
    computed: {
        loggingIn() {
            return this.$store.state.authentication.status.loggingIn;
        },
        registering() {
            return this.$store.state.authentication.status.registering;
        }
    },
    methods: {
        login() {
            this.submitted = true;
            const { email, password } = this;
            const { dispatch } = this.$store;
            if (email && password) {
                dispatch('authentication/login', { email, password });
            }
        },
        register() {
            this.r_submitted = true;
            const { r_name, r_email, r_password } = this;
            const { dispatch } = this.$store;
            if (r_name && r_email && r_password) {
                dispatch('authentication/register', { r_name, r_email, r_password });
            }
        }
    }
};
</script>