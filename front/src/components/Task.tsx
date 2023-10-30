import { Draggable } from "react-beautiful-dnd";
import comments from "../assets/comments.svg";
import subtasks from "../assets/subtasks.svg";
import dayjs from "dayjs";
import { useSelector } from "react-redux";
import { RootState } from "../store/reducers";
import { IconButton, Menu, MenuItem } from "@mui/material";
import { useState } from "react";
import dots from "../assets/trotacka.svg";
import { useAppDispatch } from "../store/useAppDispatch";
import { completeTask, updateTask } from "../store/taskReducer";
import Prompt, { Form } from "./Prompt";
import { SingleTaskDto } from "../types";

interface TaskProps {
  id: number;
  index: number;
}

export default function Task({ id, index }: TaskProps) {
  const task = useSelector((state: RootState) => state.task[id]);
  const users = useSelector((state: RootState) => state.user)
  const allLabels = useSelector((state: RootState) => state.label)
  const dispatch = useAppDispatch();

  const [dropdown, setDropdown] = useState<null | HTMLElement>(null);
  const menuOpen = Boolean(dropdown);
  const handleMenuOpen = (event: React.MouseEvent<HTMLButtonElement>) => {
    setDropdown(event.currentTarget);
  };
  const handleMenuClose = () => {
    setDropdown(null);
  };
  const [modal, setModal] = useState(false);

  const {
    isImportant,
    name,
    labels,
    commentsCount,
    openSubtasks,
    startOn,
    dueOn,
    assignee,
    isCompleted,
  } = task;
  const displayDueOnDate = (date: string | null) => {
    if (!date) return "";
    if (dayjs().year() !== Number(dayjs(date).format("YYYY"))) {
      return dayjs(date).format("MMM D. YYYY");
    } else {
      return dayjs(date).format("MMM D.");
    }
  };

  const onEditTask = ({ name, isImportant, assignee }: Form) => {
    const payload: SingleTaskDto = {
      id: task.id,
      name,
      position: task.position,
      isCompleted: task.isCompleted,
      startOn: task.startOn,
      dueOn: task.dueOn,
      taskListId: task.taskListId,
      commentsCount: task.commentsCount,
      openSubtasks: task.openSubtasks,
      isImportant,
      assignee,
      labels: task.labels,
      completedOn: task.completedOn,
    };

    dispatch(updateTask(payload));
  };

  return (
    <>
      <Draggable key={id} draggableId={`${id}`} index={index}>
        {(provided) => (
          <div
            className={`m-1 flex min-h-[100px] bg-slate-100 shadow-lg rounded-md border-[1px] overflow-hidden ${
              isImportant
                ? "before:bg-pink-600 before:w-1 before:block"
                : "before:bg-slate-100 before:w-1 before:block"
            } ${isCompleted && "before:bg-green-50 bg-green-50"}`}
            ref={provided.innerRef}
            {...provided.draggableProps}
            {...provided.dragHandleProps}
            data-id={id}
          >
            <div className="flex-1 p-2">
              <p className={`${isCompleted && "line-through"} flex items-baseline justify-between`}>
                <span>
                  #{id} {name}
                </span>
                {!isCompleted &&
                <>
                  <IconButton
                    onClick={handleMenuOpen}
                    aria-controls={menuOpen ? "basic-menu" : undefined}
                    aria-haspopup="true"
                    aria-expanded={menuOpen ? "true" : undefined}
                  >
                    <img src={dots} alt="dots" className="cursor-pointer" />
                  </IconButton>
                  <Menu
                    open={menuOpen}
                    onClose={handleMenuClose}
                    sx={{
                      borderRadius: 8,
                    }}
                    anchorEl={dropdown}
                  >
                    <MenuItem onClick={() => setModal(true)}>Edit</MenuItem>
                    <MenuItem onClick={() => dispatch(completeTask(id))}>Complete</MenuItem>
                  </Menu>
                </>
                }
              </p>
              <div className="flex gap-2 items-center mt-2">
                {labels ? labels.length > 5 ? (
                  <div className="flex gap-1 items-center">
                    {[...labels].slice(0, 5).map(({id}, index) => {
                      return (
                        <div
                          key={index}
                          className="w-2 h-2 rounded-full"
                          style={{
                            backgroundColor: allLabels[id]?.color,
                          }}
                        />
                      );
                    })}
                    <span className="text-gray-400 text-sm">
                      +{labels.length - 5}
                    </span>
                  </div>
                ) : labels.length > 0 ? (
                  <div className="flex gap-1">
                    {labels.map(({id}) => {
                      return (
                        <div
                          key={id}
                          className="w-2 h-2 rounded-full"
                          style={{
                            backgroundColor: allLabels[id]?.color,
                          }}
                        ></div>
                      );
                    })}
                  </div>
                ) : (
                  <></>
                ): null }
                {commentsCount > 0 && (
                  <div className="flex gap-1 items-center">
                    <img src={subtasks} alt="comment_icon" className="w-4" />
                    <span className="text-gray-400 text-sm">
                      {commentsCount}
                    </span>
                  </div>
                )}
                {openSubtasks > 0 && (
                  <div className="flex gap-1 items-center">
                    <img src={comments} alt="subtasks_icon" className="w-4" />
                    <span className="text-gray-400 text-sm">{openSubtasks}</span>
                  </div>
                )}
              </div>
              <div className="flex justify-between items-center mt-2">
                <p className="font-bold text-gray-700">
                  <span>
                    {startOn ? `${dayjs(startOn).format("MMMM D.")} - ` : ""}
                  </span>
                  <span>{displayDueOnDate(dueOn)}</span>
                </p>
                <div className="flex -space-x-2">
                  {assignee?.map(({id}, index) => {
                    const user = users[id];
                    if (!user) return null
                    const { avatarUrl, name } = user;
                    return (
                      <img
                        key={index}
                        src={avatarUrl}
                        alt={name}
                        className="w-7 h-7 rounded-full"
                      />
                    );
                  })}
                </div>
              </div>
            </div>
          </div>
        )}
      </Draggable>
      <Prompt
        title="Edit task"
        isOpen={modal}
        closeFn={() => setModal(false)}
        submitFn={onEditTask}
        task={task}
      />
    </>
  );
}
