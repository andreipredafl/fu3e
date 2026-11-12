/**
 * Hands a file to the browser as a download.
 */
export const downloadFile = (file: Blob, filename: string) => {
    const url = URL.createObjectURL(file);
    const link = document.createElement('a');

    link.href = url;
    link.download = filename;
    link.click();
    URL.revokeObjectURL(url);
};
