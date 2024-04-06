import React from 'react';
import store from './redux/store';
import { Provider } from 'react-redux';

import Users from './users/users';
import ErrorMessage from './errorMessage/errorMessage';

export default function (props) {
    return (
        <Provider store={store}>
            {props.path === 'users/users' && <Users />}
            <ErrorMessage />
        </Provider>
    )
}