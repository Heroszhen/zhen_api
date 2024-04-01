import { legacy_createStore as createStore, combineReducers,  applyMiddleware } from 'redux';
import { thunk } from 'redux-thunk';

import userReducer from './reducers/userReducer';
import errorReducer from './reducers/errorReducer';


const rootReducer = combineReducers({
    userReducer,
    errorReducer
});

// const store = createStore(
//     rootReducer, {}, window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()
// );
const store = createStore(rootReducer, applyMiddleware(thunk));
export default store