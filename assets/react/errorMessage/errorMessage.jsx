import React from 'react';
import { useSelector, useDispatch } from 'react-redux';

const ErrorMessage = () => {
    const dispatch = useDispatch();
    const errorReducer = useSelector(state => state.errorReducer);
    console.log(errorReducer);

    return (
        <div>dlkfdlkfj</div>
    )
}
export default ErrorMessage;