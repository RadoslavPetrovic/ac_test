export type ArrOfNumbers = {id: number}[];

export interface SingleLabel {
  id: number;
  color: string;
}

export interface SingleUser {
  id: number;
  name: string;
  avatarUrl: string;
}

export interface SingleTaskList {
  id: number;
  name: string;
  openTasks: number;
  completedTasks: number;
  position: number;
  isCompleted: boolean;
  isTrashed: boolean;
}

export interface SingleTask {
  id: number;
  name: string;
  isCompleted: boolean;
  taskListId: number;
  position: number;
  startOn: string | null;
  dueOn: string | null;
  labels: ArrOfNumbers;
  openSubtasks: number;
  commentsCount: number;
  assignee: ArrOfNumbers;
  isImportant: boolean;
  completedOn: string | null;
}

export interface SingleTaskDto extends Omit<SingleTask, 'assignee'> {
  assignee: number[];
}

export interface Collection<T> {
  [key: string]: T;
  [key: number]: T;
}
