import fs from "fs/promises";

export async function readData(path) {

    try {
        const data = await fs.readFile(path, "utf-8");
        return JSON.parse(data);

    } catch (error) {
        return [];
    }
}

export async function writeData(path, data) {

    await fs.writeFile(
        path,
        JSON.stringify(data, null, 2)
    );
}