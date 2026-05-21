export default function auth(req, res, next) {

    const token = req.headers.authorization;

    if (!token || token !== "Bearer mytoken") {
        return res.status(401).json({
            message: "Unauthorized"
        });
    }

    next();
}