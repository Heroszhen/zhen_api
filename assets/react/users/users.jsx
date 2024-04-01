import React, { useState, useEffect } from 'react';
import './users.scss';
import { useSelector, useDispatch } from 'react-redux';
import { asyncGetUsers } from '../redux/actions/userAction';

const Users = () => {
    const dispatch = useDispatch();
    const userReducer = useSelector(state => state.userReducer);

    useEffect(() => {
        dispatch(asyncGetUsers());
    }, []);

    return (
        <>
            <section id="users-list" className='admin-section container-fluid'>
                <h2 className="admin-section-title">Les comptes</h2>
                <table className="table">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Mail</th>
                            <th scope="col">Roles</th>
                            <th scope="col">Dates</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody className='table-striped'>
                        {
                            userReducer.users.map((user, key) => {
                                return (
                                    <tr key={key}>
                                        <th colSpan="row">{user.id}</th>
                                        <td>{user.email}</td>
                                        <td>
                                            {
                                                user.roles.map((role, key2) => {
                                                    return (
                                                        <div key={key2}>{role}</div>
                                                    )
                                                })
                                            }
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
                                            <button type="button" class="btn btn-primary me-1 mb-1">Modifer</button>
                                            <button type="button" class="btn btn-primary me-1 mb-1">Mot de passe</button>
                                            <button type="button" class="btn btn-primary me-1 mb-1">Api Key</button>
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