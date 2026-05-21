import express from "express";
import auth from "../middleware/auth.js";

import {
    getNotes,
    createNote,
    deleteNote
} from "../controllers/notesController.js";

const router = express.Router();

router.get("/", auth, getNotes);

router.post("/", auth, createNote);

router.delete("/:id", auth, deleteNote);

export default router;