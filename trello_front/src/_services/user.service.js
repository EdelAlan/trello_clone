import config from 'config';
import { authHeader } from '../_helpers';

export const userService = {
    login,
    logout,
    register,
    getUser,

    addColumn,
    getColumns,
    editColumn,
    removeColumn,
    updateColumnsPositions,

    addCard,
    getCards,
    editCard,
    removeCard,
    updateCardsPositions,
};

function login(email, password) {
    const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    };

    return fetch(`${config.apiUrl}/login`, requestOptions)
        .then(handleResponse)
        .then(res => {
            if (res.token) {
                localStorage.setItem('token', JSON.stringify(res));
            }

            return res;
        });
}

function register(name, email, password) {
    const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, password })
    };

    return fetch(`${config.apiUrl}/register`, requestOptions)
        .then(handleResponse)
        .then(res => {
            if (res.token) {
                localStorage.setItem('token', JSON.stringify(res));
            }

            return res;
        });
}

function logout() {
    localStorage.removeItem('token');
}

function getUser() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader(),
    };

    return fetch(`${config.apiUrl}/user`, requestOptions).then(handleResponse);
}

function addColumn(name) {
    const requestOptions = {
        method: 'POST',
        headers: authHeader(),
        body: JSON.stringify({ name })
    };

    return fetch(`${config.apiUrl}/columns`, requestOptions).then(handleResponse);
}
function editColumn(name, column_id) {
    const requestOptions = {
        method: 'PUT',
        headers: authHeader(),
        body: JSON.stringify({ name })
    };

    return fetch(`${config.apiUrl}/columns/${column_id}`, requestOptions).then(handleResponse);
}
function getColumns() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch(`${config.apiUrl}/columns`, requestOptions).then(handleResponse);
}
function removeColumn(column_id) {
    const requestOptions = {
        method: 'DELETE',
        headers: authHeader()
    };

    console.log(column_id);

    return fetch(`${config.apiUrl}/columns/${column_id}`, requestOptions).then(handleResponse);
}
function updateColumnsPositions(columns) {
    const requestOptions = {
        method: 'PUT',
        headers: {
            ...authHeader(),
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify(columns)
    };

    return fetch(`${config.apiUrl}/columns`, requestOptions).then(handleResponse);
}

function addCard(column_id) {
    const requestOptions = {
        method: 'POST',
        headers: authHeader(),
        body: JSON.stringify({ column_id })
    };

    return fetch(`${config.apiUrl}/cards`, requestOptions).then(handleResponse);
}
function editCard(name, card_id) {
    const requestOptions = {
        method: 'PUT',
        headers: authHeader(),
        body: JSON.stringify({ name })
    };

    return fetch(`${config.apiUrl}/cards/${card_id}`, requestOptions).then(handleResponse);
}
function getCards() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch(`${config.apiUrl}/cards`, requestOptions).then(handleResponse);
}
function removeCard(card_id) {
    const requestOptions = {
        method: 'DELETE',
        headers: authHeader()
    };

    return fetch(`${config.apiUrl}/cards/${card_id}`, requestOptions).then(handleResponse);
}
function updateCardsPositions(card) {
    const requestOptions = {
        method: 'PUT',
        headers: {
            ...authHeader(),
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify(card)
    };

    return fetch(`${config.apiUrl}/cards`, requestOptions).then(handleResponse);
}

function handleResponse(response) {
    return response.text().then(text => {
        const data = text && JSON.parse(text);
        if (!response.ok) {
            if (response.status === 401) {
                logout();
            }

            const error = (data && data.message) || response.statusText;
            return Promise.reject(error);
        }

        return data;
    });
}