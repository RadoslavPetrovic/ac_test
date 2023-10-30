import { combineReducers } from "@reduxjs/toolkit";
import { taskReducer } from "./taskReducer";
import { taskListReducer } from "./taskListReducer";
import { userReducer } from "./userReducer";
import { labelReducer } from "./labelReducer";

const rootReducer = combineReducers({
  task: taskReducer,
  taskList: taskListReducer,
  user: userReducer,
  label: labelReducer
});

export type RootState = ReturnType<typeof rootReducer>;
export default rootReducer;
