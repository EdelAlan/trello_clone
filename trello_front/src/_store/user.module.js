import { userService } from '../_services';

export const user = {
    namespaced: true,
    state: {
        user: {}
    },
    actions: {
        getUser({ commit }) {
            commit('getUserRequest');

            userService.getUser()
                .then(
                    user => commit('getUserSuccess', user),
                    error => commit('getUserFailure', error)
                );
        }
    },
    mutations: {
        getUserRequest(state) {
            state.user = { loading: true };
        },
        getUserSuccess(state, user) {
            state.user = { ...user.user };
        },
        getUserFailure(state, error) {
            state.user = { error };
        }
    }
}
