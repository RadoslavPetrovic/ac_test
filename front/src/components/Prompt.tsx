import Modal from "@mui/material/Modal";
import { useMemo, useState } from "react";
import { useSelector } from "react-redux";
import { RootState } from "../store/reducers";
import { SingleTask } from "../types";

interface Props {
  title: string;
  isOpen: boolean;
  closeFn: () => void;
  submitFn: (form: Form) => void;
  task?: SingleTask
}

export type Form = {
  name: string,
  isImportant: boolean,
  assignee: number[]
}

export default function Prompt({ title, closeFn, submitFn, isOpen, task }: Props) {
  const initialForm = useMemo(() => (
    task ? { name: task.name, isImportant: task.isImportant, assignee: task.assignee.reduce<number []>((prev, next) => { prev.push(next.id); return prev }, [])} : { name: '', isImportant: false, assignee: [] }
  ), [task])
  const [form, setForm] = useState<Form>(initialForm)
  const users = useSelector((state: RootState) => state.user)

  function handleSubmit() {
    if (!form.name.trim()) return;
    submitFn(form);
    closeFn();
    setForm(initialForm)
  }

  function addOrRemove(array: number[], value: string) {
    const index = array.indexOf(+value);

    if (index === -1) {
        array.push(+value);
    } else {
        array.splice(index, 1);
    }

    return array
  }

  return (
    <Modal open={isOpen} onClose={closeFn}>
      <div className="absolute top-[30%] left-[50%] -translate-x-[50%] -translate-y-[30%] bg-gray-300 rounded-md p-5 ">
        <h3 className="text-lg font-bold mb-5">{title}</h3>
        <form onSubmit={handleSubmit}>
          <label>
            Name: 
            <input
              type="text"
              value={form.name}
              onChange={(e) => setForm(oldForm => ({ ...oldForm, name: e.target.value }))}
              className="p-1"
              autoFocus
              required
            />
          </label>
          <br/>
          <br/>
          <label>
            Is important:
            <input
              type="checkbox"
              checked={form.isImportant}
              onChange={() => setForm(oldForm => ({ ...oldForm, isImportant: !oldForm.isImportant }))}
            />
          </label>
          <br/>
          <br/>
          <fieldset>      
            <legend>Assignee:</legend>
            {Object.keys(users)?.map((key: string) =>
            <div key={key}>
              <label>
                {users[key].name}
                <input
                  type="checkbox"
                  value={users[key].id}
                  checked={form.assignee.includes(users[key].id)}
                  onChange={(e) => setForm(oldForm => ({ ...oldForm, assignee: addOrRemove(oldForm.assignee, e.target.value) }))}
                />
              </label>
              <br/>
            </div>
            )}
          </fieldset>
          <br/>
          <br/>
          <button className="block mt-3 text-white bg-violet-700 px-3 py-1 rounded-md shadow-md">
            Submit
          </button>
        </form>
      </div>
    </Modal>
  );
}
