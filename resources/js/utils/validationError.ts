import axios from 'axios';

export interface ValidationError {
    errors: Record<string, string[]>;
}

/**
 * Returns Laravel's validation error from a failed request, or null when the
 * request failed for another reason or the error cannot be read.
 */
export const readValidationError = async (error: unknown): Promise<ValidationError | null> => {
    if (!axios.isAxiosError(error) || error.response?.status !== 422) {
        return null;
    }

    const body = error.response.data;

    try {
        return body instanceof Blob ? JSON.parse(await body.text()) : body;
    } catch {
        return null;
    }
};
