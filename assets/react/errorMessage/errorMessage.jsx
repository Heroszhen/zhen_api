import React, {useEffect} from 'react';
import { useSelector, useDispatch } from 'react-redux';
import './errorMessage.scss';
import { DELETE_MSG, TYPE_ERROR, TYPE_SUCCESS } from '../redux/reducers/errorReducer';
import 'react-notifications/lib/notifications.css';
import {NotificationContainer, NotificationManager} from 'react-notifications';

const ErrorMessage = () => {
    const dispatch = useDispatch();
    const errorReducer = useSelector(state => state.errorReducer);
    let msg = "";

    useEffect(() => {
        if (errorReducer.message?.type === TYPE_SUCCESS) {
            msg = '';
            errorReducer.message.messages.forEach(item => msg += item);
            NotificationManager.success(msg, 'Enregistré');
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
            <NotificationContainer />
        </React.Fragment>
    )
}
export default ErrorMessage;

