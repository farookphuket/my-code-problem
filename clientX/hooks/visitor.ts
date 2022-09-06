import { useState } from "react";
import axios from "../lib/axios";
export const useVisitor = () => {
  const [visitor, setVisitor] = useState("");

  const getVisitor = async () => {
    let res = await axios.get("/api/visitor");
    let data = await res.data.visitor;
    setVisitor(data);
  };

  return {
    visitor,
    getVisitor,
  };
};

export default useVisitor;
