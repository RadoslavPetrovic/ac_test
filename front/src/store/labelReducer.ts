import { createAsyncThunk, createReducer } from "@reduxjs/toolkit";
import { Collection, SingleLabel } from "../types";

const initialState = {};

export const fetchLabels = createAsyncThunk('labels/fetchLabels', async () => {
  const response = await fetch('http://127.0.0.1:8000/labels', {
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
  })
  const data = response.json()
  return data
})

export const labelReducer = createReducer<Collection<SingleLabel>>(
  initialState,
  (builder) => {
    builder
      .addCase(fetchLabels.fulfilled, (state, action) => {
        const labels =(action.payload as SingleLabel[]).reduce<Collection<SingleLabel>>((prev, next) => {
          prev[next.id] = next;
          return prev;
        }, {});

        return {...state, ...labels};
      });
  },
);
