import React, { useState, useEffect } from 'react';
import './users.scss';
import { useSelector, useDispatch } from 'react-redux';
import { asyncGetUsers, asyncUpdateUser } from '../redux/actions/userAction';

const Users = () => {
    const dispatch = useDispatch();
    const userReducer = useSelector(state => state.userReducer);
    const [elmIndex, setElmIndex] = useState(null);

    useEffect(() => {
        dispatch(asyncGetUsers());
    }, []);

    const changeRole = (event, index) => {
        const value = event.target.value;
        let user = userReducer.users[index];
        if (event.target.checked === true) {
            user['roles'].push(value);
        } else {
            user['roles'] = user['roles'].filter(role => role !== value);
        }
        
        dispatch(asyncUpdateUser({roles: user['roles']}, user['id']));
    }

    const modifyEmail = (event) => {
        if (event.keyCode === 13) {
            dispatch(asyncUpdateUser({email: event.target.value}, userReducer.users[elmIndex]['id']));
            setElmIndex(null);
        }
    }

    return (
        <>
            <section id="users-list" className='admin-section container-fluid' onClick={() => setElmIndex(null)}>
                <h2 className="admin-section-title">Les comptes</h2>
                <table className="table table-hover table-striped">
                    <thead className="table-info">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Mail</th>
                            <th scope="col">Roles</th>
                            <th scope="col">Dates</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {
                            userReducer.users.map((user, key) => {
                                return (
                                    <tr key={key}>
                                        <th colSpan="row">{user.id}</th>
                                        <td>
                                            {elmIndex !== key && 
                                                <span className="pointer" onDoubleClick={() => setElmIndex(key)}>{user.email}</span>
                                            }
                                            {elmIndex === key && 
                                                <input 
                                                    className="form-control" type="text" 
                                                    defaultValue={userReducer.users[key]['email']} 
                                                    onClick={(e) => e.stopPropagation()}
                                                    onKeyUp={(e) => modifyEmail(e)}
                                                />
                                            }
                                        </td>
                                        <td>
                                            <div className="form-check">
                                                <input 
                                                    className="form-check-input" type="checkbox" 
                                                    value="ROLE_USER" 
                                                    id={user.id + "role_user"} defaultChecked={user.roles.includes('ROLE_USER')} 
                                                    onChange={(e) => changeRole(e, key)}
                                                />
                                                <label className="form-check-label" htmlFor={user.id + "role_user"}>
                                                    User
                                                </label>
                                            </div>
                                            <div className="form-check">
                                                <input 
                                                    className="form-check-input" type="checkbox" value="ROLE_ADMIN" 
                                                    id={user.id + "role_admin"} defaultChecked={user.roles.includes('ROLE_ADMIN')} 
                                                    onChange={(e) => changeRole(e, key)}
                                                />
                                                <label className="form-check-label" htmlFor={user.id + "role_admin"}>
                                                    Admin
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                Créé: {user.created}
                                            </div>
                                            <div>
                                                Modifé: {user.updated}
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" className="btn btn-info me-1 mb-1">Mot de passe</button>
                                            <button type="button" className="btn btn-dark me-1 mb-1">Api Key</button>
                                        </td>
                                    </tr>
                                )
                            })
                        }
                    </tbody>
                </table>
            </section>
        </>
    )
}
export default Users;