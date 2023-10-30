import { createSelector } from "@reduxjs/toolkit";
import { RootState } from "./reducers";
import { Collection } from "../types";

const getTasks = (state: RootState) => state.task;
const getTaskLists = (state: RootState) => state.taskList;
const getUsers = (state: RootState) => state.user;
const getLabels = (state: RootState) => state.label;

const getTasksArray = createSelector(getTasks, (tasks) => Object.values(tasks));

export const getTaskListsArray = createSelector(getTaskLists, (taskLists) =>
  Object.values(taskLists),
);

export const getUsersArray = createSelector(getUsers, (users) => 
  Object.values(users),
);

export const getLabelsArray = createSelector(getLabels, (labels) => 
  Object.values(labels),
);

const sortByPosition = (a: { position: number }, b: { position: number }) => {
  if (a.position === b.position) {
    return 0;
  }
  return a.position < b.position ? -1 : 1;
};

export const getTasksByTaskListId = createSelector(
  getTasksArray,
  (_state: RootState, id: number) => id,
  (tasks, taskListId) => {
    return tasks
      .filter((task) => task.taskListId === taskListId && !task.isCompleted)
      .sort(sortByPosition);
  },
);

export const getTaskListsSorted = createSelector(
  getTaskListsArray,
  (taskLists) => {
    return taskLists
      .filter((taskList) => !taskList.isCompleted && !taskList.isTrashed)
      .sort((a, b) => {
        return a.position - b.position;
      });
  },
);

export const getCompletedTasks = createSelector(getTasksArray, (tasks) => {
  return tasks
    .filter((t) => t.isCompleted)
    .sort((a, b) => {
      if (a.completedOn && b.completedOn) {
        if (a.completedOn === b.completedOn) {
          return 0;
        }
        return Date.parse(a.completedOn) > Date.parse(b.completedOn) ? -1 : 1;
      }
      return sortByPosition(a, b);
    });
});

export const getTasksByTaskListsIds = createSelector(
  getTasks,
  getCompletedTasks,
  (tasks, completedTasks) => {
    return Object.values(tasks).reduce<Collection<number[]>>(
      (prev, next) => {
        const key = next.taskListId;
        if (!prev[key]) {
          prev[key] = [];
        }
        prev[key].push(next.id);
        prev[key].sort((a, b) => {
          return sortByPosition(tasks[a], tasks[b]);
        });
        return prev;
      },
      {
        "completed-list": completedTasks.map((t) => t.id),
      },
    );
  },
);
