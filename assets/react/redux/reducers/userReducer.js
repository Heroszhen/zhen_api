import { GET_USERS } from "../actions/userAction";

const initialState = {
    users: []
};

export default function userReducer(state = initialState, action) {
    switch(action.type){
        case GET_USERS:
            return {users: action.payload};
        default:
            return state
    }
}