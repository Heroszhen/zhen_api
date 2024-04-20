import { GET_USERS, UPDATE_USER, ADD_USER, DELETE_USER } from "../actions/userAction";

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
        case ADD_USER:
            return {
                ...state,
                users: [...state.users, action.payload]
            };  
        case DELETE_USER:
            return {
                ...state,
                users: state.users.filter(user => user.id !== action.payload.id)
            };  
        default:
            return state
    }
}