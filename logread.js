const fs = require('fs');
const buffer = Buffer.alloc(10000);
const filePath = 'd:\\laragon\\www\\ticketing\\debug_notification.json';

try {
    const fd = fs.openSync(filePath, 'r');
    const stats = fs.fstatSync(fd);
    const size = stats.size;
    const start = Math.max(0, size - 10000);
    const length = size - start;

    fs.readSync(fd, buffer, 0, length, start);
    console.log(buffer.toString('utf8', 0, length));
    fs.closeSync(fd);
} catch (err) {
    console.error('Error:', err.message);
}
