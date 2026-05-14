CREATE TABLE IF NOT EXISTS questions (
    id SERIAL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    answers TEXT[] NOT NULL,
    correct_answer_index INTEGER NOT NULL CHECK (
        correct_answer_index >= 0 AND
        correct_answer_index < array_length(answers, 1)
    ),
    position INTEGER NOT NULL,
    test_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);