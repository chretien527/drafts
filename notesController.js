import { v4 as uuidv4 } from "uuid";

import {
    readData,
    writeData
} from "../utils/fileHandler.js";

const FILE_PATH = "./data/notes.json";

export async function getNotes(req, res) {

    const notes = await readData(FILE_PATH);

    res.json(notes);
}

export async function createNote(req, res) {

    const { title, content } = req.body;

    if (!title || !content) {
        return res.status(400).json({
            message: "All fields required"
        });
    }

    const notes = await readData(FILE_PATH);

    const newNote = {
        id: uuidv4(),
        title,
        content,
        createdAt: new Date()
    };

    notes.push(newNote);

    await writeData(FILE_PATH, notes);

    res.status(201).json(newNote);
}

export async function deleteNote(req, res) {

    const { id } = req.params;

    const notes = await readData(FILE_PATH);

    const filtered = notes.filter(note => note.id !== id);

    await writeData(FILE_PATH, filtered);

    res.json({
        message: "Note deleted"
    });
}