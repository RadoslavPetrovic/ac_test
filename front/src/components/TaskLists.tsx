import { DragDropContext, DropResult } from "react-beautiful-dnd";
import TaskList from "./TaskList";
import { useSelector } from "react-redux";
import { getTaskListsSorted, getTasksByTaskListsIds } from "../store/selectors";
import { useCallback, useEffect, useState } from "react";
import Prompt, { Form } from "./Prompt";
import { SingleTaskList } from "../types";
import {
  completeTask,
  fetchTasks,
  reopenTask,
  updateTaskPosition,
} from "../store/taskReducer";
import CompletedTaskList from "./CompletedTaskList";
import { createTaskList, fetchTaskLists } from "../store/taskListReducer";
import { useAppDispatch } from "../store/useAppDispatch";
import { fetchUsers } from "../store/userReducer";
import { fetchLabels } from "../store/labelReducer";

export const TaskLists = () => {
  const dispatch = useAppDispatch();
  const taskLists = useSelector(getTaskListsSorted);
  const tasksByTaskList = useSelector(getTasksByTaskListsIds);
  const [modal, setModal] = useState(false);

  useEffect(() => {
  // Initial fetch
  dispatch(fetchUsers())
  dispatch(fetchLabels())
  dispatch(fetchTasks())
  dispatch(fetchTaskLists())
  }, [dispatch])

  const calculatePositions = useCallback(
    (
      sourcePos: number,
      destinationPos: number,
      sourceListId: number | string,
      destinationListId: number | string,
    ) => {
      if (sourceListId === destinationListId) {
        const sourcePositions = tasksByTaskList[sourceListId];
        const result = Array.from(sourcePositions);
        const [removed] = result.splice(sourcePos, 1);
        result.splice(destinationPos, 0, removed);
        return {
          [sourceListId]: result,
        };
      } else {
        const sourcePositions = tasksByTaskList[sourceListId] || [];
        const destinationPosition = tasksByTaskList[destinationListId] || [];
        const resultSource = Array.from(sourcePositions);
        const resultDestination = Array.from(destinationPosition);
        const [removed] = resultSource.splice(sourcePos, 1);
        resultDestination.splice(destinationPos, 0, removed);
        return {
          [sourceListId]: resultSource,
          [destinationListId]: resultDestination,
        };
      }
    },
    [tasksByTaskList],
  );

  const handleDragEnd = useCallback(
    (result: DropResult) => {
      if (!result.destination) return;

      const { source, destination, draggableId } = result;

      if (
        source.droppableId === "completed-list" &&
        destination?.droppableId === "completed-list"
      ) {
        return;
      }

      if (destination?.droppableId === "completed-list") {
        dispatch(completeTask(Number(draggableId)));
        return;
      }

      if (source.droppableId === "completed-list") {
        dispatch(
          reopenTask({
            id: Number(draggableId),
            taskListId: Number(destination?.droppableId),
          }),
        );
      }

      const pos = calculatePositions(
        source.index,
        destination?.index,
        source.droppableId,
        destination?.droppableId,
      );

      if (pos) {
        const { "completed-list": completed, ...payload } = pos;
        dispatch(updateTaskPosition(payload));
      }
    },
    [calculatePositions, dispatch],
  );

  const handleAddNewList = ({ name }: Form) => {
    const lastPosition = taskLists[taskLists.length - 1]?.position || 0;
    const list: SingleTaskList = {
      id: Date.now(),
      name,
      position: lastPosition + 1,
      isCompleted: false,
      isTrashed: false,
      openTasks: 0,
      completedTasks: 0,
    };
    dispatch(createTaskList(list));
  };
  return (
    <div className="flex gap-3">
      <DragDropContext onDragEnd={handleDragEnd}>
        {taskLists.map((taskList, index) => {
          return <TaskList key={index} id={taskList.id} />;
        })}
        <div
          onClick={() => setModal(true)}
          className="px-4 py-2 pt-1 text-3xl shadow-lg bg-slate-100 h-fit rounded-md flex justify-center items-center border-slate-200 text-slate-400 border-[1px] cursor-pointer"
        >
          <span>+</span>
        </div>
        <CompletedTaskList />
      </DragDropContext>
      <Prompt
        title="Add new task list"
        isOpen={modal}
        closeFn={() => setModal(false)}
        submitFn={handleAddNewList}
      />
    </div>
  );
};
