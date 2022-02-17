import router from "@/router/index"
import { fetchInitUserData } from "../events"
import AuthService from "@/services/api/AuthService"

export const namespaced = true
export const state = {
    user: JSON.parse(window.localStorage.getItem("user")), //- TODO: Look into a better place to store this?
    error: false,
    loading: false,
    token: window.localStorage.getItem("token"),
}

export const mutations = {
    SET_TOKEN(state, token) {
        state.token = token
        window.localStorage.setItem("token", token)
    },
    SET_LOADING(state, boolean) {
        state.loading = boolean
    },
    SET_ERROR(state, errorText) {
        state.error = errorText
    },
    SET_USER(state, user) {
        state.user = user
        window.localStorage.setItem("user", JSON.stringify(user))
    },
    CLEAR_USER() {
        state.user = false
        window.localStorage.clear()
    },
}

export const getters = {
    loggedIn: (state) => {
        return !!state.user
    },
}

export const actions = {
    login({ commit, dispatch }, params) {
        commit("SET_LOADING", true)

        return AuthService.login(params)
            .then(async (response) => {
                commit("SET_TOKEN", response.data.token)
                commit("SET_USER", response.data.user)
                commit("SET_LOADING", false)
                commit("SET_ERROR", false)

                // Fetch init user data
                await fetchInitUserData()
            })
            .catch((error) => {
                commit("SET_ERROR", error)
            })
    },
    logout({ commit, getters }) {
        if (!getters.loggedIn) {
            return true
        }

        commit("CLEAR_USER")

        return true
    },
    getUser({ commit, getters, state }) {
        if (getters.loggedIn) {
            return
        }

        if (!state.token) {
            console.error("Can not get user info without token")
            return
        }

        return AuthService.getUser()
            .then((response) => {
                if (response.data) {
                    commit("SET_USER", response.data)
                }
            })
            .catch((error) => {
                if (router.history.current.path !== "/login") {
                    router.push("/login")
                }
            })
    },
}
