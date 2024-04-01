import { GET_USERS, UPDATE_USER } from "../actions/userAction";

const initialState = {
    users: []
};

export default function userReducer(state = initialState, action) {
    switch(action.type){
        case GET_USERS:
            return {users: action.payload};

        case UPDATE_USER:
            return {
                ...state,
                users: state.users.map(elm => {
                    if(elm.id === action.payload.id)return action.payload
                    return elm;
                })
            };

        default:
            return state
    }
}