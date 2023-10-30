import React, { ChangeEvent, useCallback, useState } from "react";
import { Provider } from "react-redux";
import { store } from "./store/store";
import { TaskLists } from "./components/TaskLists";

const App = () => {
  const [token, setToken] = useState(sessionStorage.getItem('token'))
  const [form, setForm] = useState({ username: '', password: '' })

  const handleChange = useCallback(({ target }: ChangeEvent<HTMLInputElement>) => {
    setForm(oldForm => ({...oldForm, [target.id]: target.value}))
  }, [])

  const handleLogin = useCallback(async() => {
    const response = await fetch('http://127.0.0.1:8000/login_check', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(form)
    })
    const data = await response.json()
    if (data.token) {
      sessionStorage.setItem('token', data.token)
      setToken(data.token)
    } else {
      alert('User not found!')
    }
  }, [form])

  return (
    <Provider store={store}>
      <div className="m-auto mt-10 w-fit">
        {token ?
          <TaskLists />
        :
          <div className="flex items-center flex-col gap-3">
            <h2 className="text-2xl">Login</h2>
            <label>
              Username:
              <br/>
              <input className="border-2" value={form.username} onChange={handleChange} id='username' type='username' />
            </label>
            <br/>
            <label>
              Password: 
              <br/>
              <input className="border-2" value={form.password} onChange={handleChange} id='password' type='password' />
            </label>
            <br/>
            <button className="border-2" onClick={handleLogin}>Submit</button>
          </div>
        }
      </div>
    </Provider>
  );
};

export default App;
