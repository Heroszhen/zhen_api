import React, { useState, useEffect } from 'react';
import './users.scss';
import { getToken } from '../utilService';
import Button from '@mui/material/Button';

export default function () {
    useEffect(() => {
        getUsers();
    }, []);

    const getUsers = async () => {
        const token = (await getToken())['data'];
        fetch(
            '/api/users',
            {headers: {
                'Authorization': `Bearer ${token}`,
                'Content-type': 'application/ld+json'
            }}
        )
            .then(response=> response.json())
            .then(json => {
               console.log(json)
            })
    }

    return (
        <>
            <section id="users-list">
            <Button variant="contained">Hello world</Button>
            </section>
        </>
    )
}