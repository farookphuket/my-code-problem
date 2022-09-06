import { useState } from "react";
import { GuestLayout } from "../components/layouts";

import { InputText } from "../components/form";
import { Button } from "../components/form";
import { useAuth } from "../hooks/auth";

import { getCookie } from "cookies-next";
export const Login = () => {
  const { login } = useAuth();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setErrors] = useState([]);
  const [status, setStatus] = useState(null);

  const u = getCookie("XSRF-TOKEN");
  console.log(u);
  const submitForm = async (e) => {
    e.preventDefault();
    login({ setErrors, setStatus, email, password });
  };
  return (
    <GuestLayout title="Login">
      <div className="p-2">
        <h1>this is the login page</h1>

        <div className="formGroup">
          <form action="" onSubmit={submitForm}>
            <div className="mb-4">
              <label className="formLabel uppercase" htmlFor="email">
                email
              </label>
              <InputText
                name="email"
                type="email"
                placeholder="Email..."
                onBlur={(e) => setEmail(e.target.value)}
              ></InputText>
            </div>
            <div className="mb-6">
              <label className="formLabel uppercase" htmlFor="">
                password
              </label>
              <InputText
                name="password"
                type="password"
                placeholder="~~~~"
                onBlur={(p) => setPassword(p.target.value)}
              />
            </div>
            <div className="block md:flex md:justify-end">
              <div className="w-full border-2 text-center">{status}</div>
              <Button type="submit" className="btn" Label="Submit" />

              <button>text</button>
            </div>
          </form>
        </div>
      </div>
    </GuestLayout>
  );
};

export default Login;
