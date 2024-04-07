import React, { useState, useEffect, useRef } from 'react';
import './users.scss';
import { useSelector, useDispatch } from 'react-redux';
import { asyncGetUsers, asyncUpdateUser, asyncAddUser, ADD_USER } from '../redux/actions/userAction';
import { useForm } from "react-hook-form";

const Users = () => {
    const dispatch = useDispatch();
    const userReducer = useSelector(state => state.userReducer);
    const [elmIndex, setElmIndex] = useState(null);
    const btnModalRef = useRef(null);
    const [modalAction, setModalAction] = useState('');
    const ACTION_ADD_USER = "add_user";
    const ACTION_EDIT_USER = "edit_user";
    const ACTION_PASSWORD = "edit_password";
    const ACTION_APIKEY = "edit_apikey";
    const { register, handleSubmit, formState: { errors }, reset } = useForm();

    useEffect(() => {
        dispatch(asyncGetUsers());
    }, []);

    const changeRole = (event, index) => {
        if (userReducer.users[index].id === 1) {
            return;
        }

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

    const switchModal = (openModal, action = '', index = null) => {
        if (action === ADD_USER) {
            reset(formValues => ({
                ...formValues,
                email: null,
                password: null,
                roles: ["ROLE_USER"]
            }))
        }
        setModalAction(action);
        if (openModal === true) {
            btnModalRef.current.click();
        }
        setElmIndex(index)
    }

    const switchRole = (event) => {}

    const submitEditUserForm = (data) => {
        if (!Array.isArray(data['roles'])) {
            data['roles'] = [data['roles']];
        }
        dispatch(asyncAddUser(data));
    }

    return (
        <>
            <section id="users-list" className='admin-section container-fluid' onClick={() => setElmIndex(null)}>
                <h2 className="admin-section-title">
                    Les comptes
                    <span className="pointer ms-2" onClick={() => switchModal(true, ACTION_ADD_USER)}>
                        <i className="bi bi-plus-circle"></i>
                    </span>
                </h2>
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
                                                    className="form-control" type="email" 
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
                                                    id={user.id + "_role_user"} defaultChecked={user.roles.includes('ROLE_USER')} 
                                                    readOnly={user.id===1}
                                                    onChange={(e) => changeRole(e, key)}
                                                />
                                                <label className="form-check-label" htmlFor={user.id + "_role_user"}>
                                                    User
                                                </label>
                                            </div>
                                            <div className="form-check">
                                                <input 
                                                    className="form-check-input" type="checkbox" value="ROLE_ADMIN" 
                                                    id={user.id + "_role_admin"} defaultChecked={user.roles.includes('ROLE_ADMIN')}
                                                    readOnly={user.id===1}
                                                    onChange={(e) => changeRole(e, key)}
                                                />
                                                <label className="form-check-label" htmlFor={user.id + "_role_admin"}>
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
                                            <button type="button" className="btn btn-info me-1 mb-1" onClick={() => switchModal(true, ACTION_PASSWORD, key)}>Mot de passe</button>
                                            <button type="button" className="btn btn-dark me-1 mb-1">Api Key</button>
                                        </td>
                                    </tr>
                                )
                            })
                        }
                    </tbody>
                </table>
            </section>

            <button type="button" className="d-none" data-bs-toggle="modal" data-bs-target="#users-modal" ref={btnModalRef}></button>
            <div className="modal fade" id="users-modal" tabIndex="-1" aria-labelledby="usersModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="usersModalLabel">
                                {modalAction === ACTION_ADD_USER && "Ajouter un compte"}
                                {modalAction === ACTION_EDIT_USER && "Modifier un compte"}
                                {modalAction === ACTION_PASSWORD && "Modifier le mot de passe"}
                                {modalAction === ACTION_APIKEY && "Modifier api key"}
                            </h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            {modalAction === ACTION_ADD_USER &&
                                <form onSubmit={handleSubmit(submitEditUserForm)}>
                                    <div className="mb-3">
                                        <label htmlFor="email">Mail *</label>
                                        <input
                                            className="form-control"
                                            type="text" id="email" name="email"
                                            {...register("email", { 
                                                required: { value: true, message: 'Le champ est obligatoire'},
                                                pattern: {
                                                    value: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
                                                    message: 'Invalide mail adresse',
                                                }
                                            })}
                                        />
                                        {errors.email && <div className="alert alert-danger mt-1">{errors.email.message}</div>}
                                    </div>
                                    <div className="mb-3">
                                        <label htmlFor="password">Mot de passe *</label>
                                        <input
                                            className="form-control"
                                            type="password" id="password" name="password"
                                            autoComplete='off'
                                            {...register("password", { 
                                                required: { 
                                                    value: true, 
                                                    message: 'Le champ est obligatoire'
                                                },
                                                minLength: {
                                                    value: 8,
                                                    message: "8 caractères au moins"
                                                }
                                            })}
                                        />
                                        {errors.password && <div className="alert alert-danger mt-1">{errors.password.message}</div>}
                                    </div>
                                    <div className='mb-3'>
                                        <label>Rôles *</label>
                                        <div className="form-check">
                                            <input 
                                                className="form-check-input"      type="checkbox" 
                                                value="ROLE_USER" 
                                                id="role-user" 
                                                name="roles" defaultChecked={true}
                                                onChange={(e)=>switchRole(e)}
                                                {...register("roles", {value: ['ROLE_USER']})}
                                            />
                                            <label className="form-check-label" htmlFor="role-user">User</label>
                                        </div>
                                        <div className="form-check">
                                            <input 
                                                className="form-check-input"      type="checkbox" 
                                                value="ROLE_ADMIN" 
                                                id="role-admin" 
                                                name="roles" defaultChecked={false}
                                                onChange={(e)=>switchRole(e)}
                                                {...register("roles", {value: ['ROLE_ADMIN']})}
                                            />
                                            <label className="form-check-label" htmlFor="role-admin">Admin</label>
                                        </div>
                                    </div>
                                    <div className="text-center">
                                        <button type="submit" className="btn btn-primary">Envoyer</button>
                                    </div>
                                </form>
                            }
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
export default Users;