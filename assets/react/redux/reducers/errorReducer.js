export const ADD_MSG = "add_message";
export const DELETE_MSG = "delete_message";
export const TYPE_SUCCESS = "sucess";
export const TYPE_ERROR = "danger";

/**
 * {
 *  type: string,
 *  messages: []
 * }
 */
const initialState = {
    message: null
};

export default function errorReducer(state = initialState, action) {
    switch(action.type) {
        case ADD_MSG:
            return {
                ...state,
                message: action.payload
            };

        case DELETE_MSG:
            return {
                ...state,
                message: null
            };

        default:
            return state
    }
}