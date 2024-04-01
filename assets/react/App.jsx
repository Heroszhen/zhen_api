import React from 'react';
import store from './redux/store';
import { Provider } from 'react-redux';

import Users from './users/users';
import ErrorMessage from './errorMessage/errorMessage';

export default function (prop) {
    return (
        <Provider store={store}>
            {prop.path === 'users/users' && <Users />}
            <ErrorMessage />
        </Provider>
    )
}