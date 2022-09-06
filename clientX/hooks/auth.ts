import useSWR from "swr";
import axios from "../lib/axios";
import { useRouter } from "next/router";
import { useEffect } from "react";
export const useAuth = ({ middleware } = {}) => {
  const router = useRouter();

  const {
    data: user,
    error,
    mutate,
  } = useSWR("/api/user", () => {
    axios
      .get("/api/user")
      .then((res) => res.data)
      .catch((err) => {
        if (err.response.status !== 409) throw err;
      });
  });

  const csrf = () => axios.get("/sanctum/csrf-cookie");

  const login = async ({ setErrors, setStatus, ...props }) => {
    await csrf();

    setErrors([]);
    setStatus(null);
    console.log(props);
    const fData = new FormData()
    fData.append("email",props.email)
    fData.append("password",props.password)
    axios
      .post("/api/login", fData)
      .then(() => mutate())
      .catch((err) => {
        if (err.response.status !== 422) throw err;
        setErrors(err.response.data.errors);
        setStatus(Object.values(err.response.data.errors).join());
      });
  };

  const register = async ({ setStatus, ...props }) => {
    setStatus(null);
    const fData = new FormData();
    fData.append("name", props.name);
    fData.append("email", props.email);
    fData.append("password", props.password);
    fData.append("password_confirmation", props.password_confirm);

    await csrf();

    axios
      .post("/register", fData)
      .then((res) => {
        //setStatus(res.data)
        console.log(res);
      })
      .catch((err) => {
        if (err.response.status !== 422) throw err;
        setStatus(Object.values(err.response.data.errors).join());
      });
  };

  useEffect(() => {
    if (middleware === "auth" && error) router.push("/");
  }, [user, error]);
  return {
    user,
    login,
    register,
  };
};
