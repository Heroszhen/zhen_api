import React, {useEffect} from 'react';
import { useSelector, useDispatch } from 'react-redux';
import './errorMessage.scss';
import { DELETE_MSG, TYPE_ERROR, TYPE_SUCCESS } from '../redux/reducers/errorReducer';

const ErrorMessage = () => {
    const dispatch = useDispatch();
    const errorReducer = useSelector(state => state.errorReducer);

    useEffect(() => {
        if (errorReducer.message?.type === TYPE_SUCCESS) {
            alert("ok")
        }
    }, [errorReducer]);

    return (
        <React.Fragment>
            {(errorReducer.message !== null && errorReducer.message.type === TYPE_ERROR) &&
                <div id="error-message">
                    <section className={errorReducer.message.type}>
                        {
                            errorReducer.message.messages.map((msg, key) => {
                                return (
                                    <article key={key}>{msg['message']}</article>
                                )
                            })
                        }
                        <div className="text-center mt-5">
                            <button 
                                type="button" 
                                className={'btn btn-' + errorReducer.message.type}
                                onClick={() => dispatch({type: DELETE_MSG})}
                            >OK</button>
                        </div>
                    </section>
                </div>
            }
        </React.Fragment>
    )
}
export default ErrorMessage;

