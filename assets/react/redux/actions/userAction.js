import { getToken } from "../../utilService";

export const GET_USERS = "get_users";

export const asyncGetUsers = () => {
    return async (dispatch) => {
        const token = (await getToken())['data'];
        return fetch(
            '/api/users',
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