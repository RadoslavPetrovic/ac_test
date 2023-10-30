import { createAsyncThunk, createReducer } from "@reduxjs/toolkit";
import { Collection, SingleUser } from "../types";

const initialState = {};

export const fetchUsers = createAsyncThunk('user/fetchUsers', async () => {
  const response = await fetch('http://127.0.0.1:8000/users', {
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
  })
  const data = response.json()
  return data
})

export const userReducer = createReducer<Collection<SingleUser>>(
  initialState,
  (builder) => {
    builder
      .addCase(fetchUsers.fulfilled, (state, action) => {
        const users =(action.payload as SingleUser[]).reduce<Collection<SingleUser>>((prev, next) => {
          prev[next.id] = next;
          return prev;
        }, {});

        return {...state, ...users};
      });
  },
);
