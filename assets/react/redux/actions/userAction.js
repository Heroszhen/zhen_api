import { getToken, getHeaders } from "../../utilService";
import { ADD_MSG, TYPE_ERROR, TYPE_SUCCESS } from "../reducers/errorReducer";

export const GET_USERS = "get_users";
export const GET_USER = "get_user";
export const UPDATE_USER = "update_user";
const ROUTE_PREFIX = "/api/users";

export const asyncGetUsers = () => {
    return async (dispatch) => {
        const token = (await getToken())['data'];
        return fetch(
            ROUTE_PREFIX,
            {headers: {
                'Authorization': `Bearer ${token}`,
                'Content-type': 'application/ld+json'
            }}
        )
        .then(response=> response.json())
        .then(json => {
            dispatch({ type: GET_USERS, payload: json['hydra:member'] });
        });
        
    }
}

export const asyncUpdateUser = (user, id) => async (dispatch) => {
    const headers = await getHeaders('patch');
    fetch(`${ROUTE_PREFIX}/${id}`, {
        headers: headers,
        method: 'PATCH',
        body: JSON.stringify(user)
    })
    .then(response => response.json())
    .then(json => {
        if (json['violations'] !== undefined) {
            dispatch({ type: ADD_MSG, payload: {type: TYPE_ERROR, messages:json['violations']} });
        } else {
            dispatch({ type: ADD_MSG, payload: {type: TYPE_SUCCESS, messages:[]} });
            dispatch({ type: UPDATE_USER, payload: json });
        } 
    })
    .catch(error => {
        
    });
}