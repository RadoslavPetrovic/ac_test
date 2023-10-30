import { createAsyncThunk, createReducer } from "@reduxjs/toolkit";
import { Collection, SingleTask, SingleTaskDto } from "../types";
import { completeTaskList } from "./taskListReducer";

const initialState = {}

export const fetchTasks = createAsyncThunk('task/fetchTasks', async () => {
  const response = await fetch('http://127.0.0.1:8000/tasks', {
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
  })
  const data = response.json()
  return data
})

export const createTask = createAsyncThunk<SingleTask, SingleTaskDto>('task/createTask', async payload => {
  const response = await fetch('http://127.0.0.1:8000/task', {
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

export const updateTask = createAsyncThunk<SingleTask, SingleTaskDto>('task/updateTask', async payload => {
  const response = await fetch('http://127.0.0.1:8000/task', {
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

export const completeTask = createAsyncThunk<SingleTask, number>('task/completeTask', async payload => {
  const response = await fetch(`http://127.0.0.1:8000/task/complete/${payload}`, {
    method: 'PUT',
    headers: {
      'Content-Type' :'application/json',
      'Authorization': `Bearer ${sessionStorage.getItem('token')}`
    },
  })
  const data = response.json()
  return data
})

export const reopenTask = createAsyncThunk<SingleTask, { id: number, taskListId?: number }>('task/reopenTask', async payload => {
  const response = await fetch(`http://127.0.0.1:8000/task/reopen`, {
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

export const updateTaskPosition = createAsyncThunk<Collection<number[]>, Collection<number[]>>('task/updateTaskPositions', async payload => {
  const response = await fetch(`http://127.0.0.1:8000/task/updateTaskPosition`, {
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

export const taskReducer = createReducer<Collection<SingleTask>>(
  initialState,
  (builder) => {
    builder
      .addCase(completeTaskList.fulfilled, (state, action) => {
        Object.values(state).forEach((t) => {
          if (t.taskListId === action.payload.id) {
            state[t.id] = {
              ...state[t.id],
              isCompleted: true,
              completedOn: new Date().toUTCString(),
            };
          }
        });
      })
      .addCase(fetchTasks.fulfilled, (state, action) => {
        const tasks =(action.payload as SingleTask[]).reduce<Collection<SingleTask>>((prev, next) => {
          prev[next.id] = next;
          return prev;
        }, {});

        return {...state, ...tasks};
      })
      .addCase(createTask.fulfilled, (state, action) => {
        return {
          ...state,
          [action.payload.id]: action.payload,
        };
      })
      .addCase(updateTask.fulfilled, (state, action) => {
        return {
          ...state,
          [action.payload.id]: action.payload,
        };
      })
      .addCase(completeTask.pending, (state, action) => {
        state[action.meta.arg] = {
          ...state[action.meta.arg],
          isCompleted: true,
          completedOn: new Date().toUTCString(),
        };
        return state;
      })
      .addCase(completeTask.fulfilled, (state, action) => {
        return {
          ...state,
          [action.payload.id]: action.payload,
        };
      })
      .addCase(reopenTask.pending, (state, action) => {
        state[action.meta.arg.id] = {
          ...state[action.meta.arg.id],
          ...action.meta.arg,
          isCompleted: true,
          completedOn: new Date().toUTCString(),
        };
        return state;
      })
      .addCase(reopenTask.fulfilled, (state, action) => {
        return {
          ...state,
          [action.payload.id]: action.payload,
        };
      })
      .addCase(updateTaskPosition.pending, (state, action) => {
        Object.keys(action.meta.arg).forEach((listId) => {
          const taskListId = Number(listId);
          action.meta.arg[listId].forEach((taskId, index) => {
            if (state[taskId]) {
              state[taskId] = {
                ...state[taskId],
                position: index,
                taskListId: taskListId,
              };
            }
          });
        });
        return state;
      })
      .addCase(updateTaskPosition.fulfilled, (state, action) => {
        Object.keys(action.payload).forEach((listId) => {
          const taskListId = Number(listId);
          action.payload[listId].forEach((taskId, index) => {
            if (state[taskId]) {
              state[taskId] = {
                ...state[taskId],
                position: index,
                taskListId: taskListId,
              };
            }
          });
        });
        return state;
      })
  },
);
