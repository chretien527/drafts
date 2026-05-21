import express from "express";
import notesRoutes from "./routes/notes.js";

const app = express();

app.use(express.json());

app.use("/api/notes", notesRoutes);

const PORT = 3000;

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});