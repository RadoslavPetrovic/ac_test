import { createAsyncThunk, createReducer } from "@reduxjs/toolkit";
import { Collection, SingleTaskList } from "../types";

const initialState = {};

export const fetchTaskLists = createAsyncThunk('taskList/fetchTaskLists', async () => {
  const response = await fetch('http://127.0.0.1:8000/taskLists', {
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
  })
  const data = response.json()
  return data
})

export const createTaskList = createAsyncThunk<SingleTaskList, SingleTaskList>('taskList/createTaskList', async payload => {
  const response = await fetch('http://127.0.0.1:8000/taskList', {
    method: 'POST',
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
    body: JSON.stringify(payload)
  })
  const data = response.json()
  return data
})

export const updateTaskList = createAsyncThunk<SingleTaskList, SingleTaskList>('taskList/updateTaskList', async payload => {
  const response = await fetch('http://127.0.0.1:8000/taskList', {
    method: 'PUT',
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
    body: JSON.stringify(payload)
  })
  const data = response.json()
  return data
})

export const completeTaskList = createAsyncThunk<SingleTaskList, number>('taskList/completeTaskLists', async payload => {
  const response = await fetch(`http://127.0.0.1:8000/taskList/complete/${payload}`, {
    method: 'PUT',
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
  })
  const data = response.json()
  return data
})

export const taskListReducer = createReducer<Collection<SingleTaskList>>(
  initialState,
  (builder) => {
    builder
      .addCase(fetchTaskLists.fulfilled, (state, action) => {
        const taskLists =(action.payload as SingleTaskList[]).reduce<Collection<SingleTaskList>>((prev, next) => {
          prev[next.id] = next;
          return prev;
        }, {});

        return {...state, ...taskLists};
      })
      .addCase(createTaskList.fulfilled, (state, action) => {
        return {
          ...state,
          [action.payload.id]: action.payload,
        };
      })
      .addCase(updateTaskList.fulfilled, (state, action) => {
        return {
          ...state,
          [action.payload.id]: action.payload,
        };
      })
      .addCase(completeTaskList.fulfilled, (state, action) => {
        return {
          ...state,
          [action.payload.id]: action.payload,
        };
      });
  },
);
