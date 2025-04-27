import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000/api", // ganti sesuai API kamu
  timeout: 5000,
});

export default api;
