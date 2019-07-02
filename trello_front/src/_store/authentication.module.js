import { userService } from '../_services';
import { router } from '../_helpers';

export const authentication = {
    namespaced: true,
    state: { status: {} },
    actions: {
        login({ dispatch, commit }, { email, password }) {
            commit('loginRequest');

            userService.login(email, password)
                .then(
                    res => {
                        if (res.success) {
                            router.push('/');
                        } else {
                            commit('loginFailure', res);
                            dispatch('alert/error', res, { root: true });
                        }
                    },
                    error => {
                        commit('loginFailure', error);
                        dispatch('alert/error', error, { root: true });
                    }
                );
        },
        register({ dispatch, commit }, { r_name, r_email, r_password }) {
            commit('registerRequest');

            userService.register(r_name, r_email, r_password)
                .then(
                    res => {
                        if (res.success) {
                            router.push('/');
                        } else {
                            commit('registerFailure', res);
                            dispatch('alert/error', res, { root: true });
                        }
                    },
                    error => {
                        commit('registerFailure', error);
                        dispatch('alert/error', error, { root: true });
                    }
                );
        },
        logout({ commit }) {
            userService.logout();
            commit('logout');
        }
    },
    mutations: {
        loginRequest(state) {
            state.status = { loggingIn: true };
        },
        loginFailure(state) {
            state.status = {};
        },

        registerRequest(state) {
            state.status = { registering: true };
        },
        registerFailure(state) {
            state.status = {};
        },

        logout(state) {
            state.status = {};
        }
    }
}
