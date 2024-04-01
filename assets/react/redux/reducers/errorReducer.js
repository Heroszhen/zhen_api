export const ADD_MSG = "add_message";
export const DELETE_MSG = "delete_message";

const initialState = {
    message: null
};

export default function errorReducer(state = initialState, action) {
    switch(action.type){
        case ADD_MSG:
            return {message: action.payload};

        case DELETE_MSG:
            return {message: null};

        default:
            return state
    }
}