
-- Create database
CREATE DATABASE IF NOT EXISTS student_lab_db;
USE student_lab_db;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    course VARCHAR(100) NOT NULL,
    year_level ENUM('1st Year', '2nd Year', '3rd Year', '4th Year') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX idx_student_id ON students(student_id);
CREATE INDEX idx_email ON students(email);
CREATE INDEX idx_name ON students(first_name, last_name);
CREATE INDEX idx_course ON students(course);
CREATE INDEX idx_year_level ON students(year_level);

-- Insert sample data
INSERT INTO students (student_id, first_name, last_name, email, phone, course, year_level) VALUES
('2024-001', 'Juan', 'Dela Cruz', 'juan.delacruz@university.edu', '+63 912 345 6789', 'Bachelor of Science in Computer Science', '3rd Year'),
('2024-002', 'Maria', 'Santos', 'maria.santos@university.edu', '+63 917 234 5678', 'Bachelor of Science in Information Technology', '2nd Year'),
('2024-003', 'Jose', 'Rizal', 'jose.rizal@university.edu', '+63 920 123 4567', 'Bachelor of Science in Engineering', '4th Year'),
('2024-004', 'Ana', 'Garcia', 'ana.garcia@university.edu', '+63 915 876 5432', 'Bachelor of Arts in Psychology', '1st Year'),
('2024-005', 'Miguel', 'Rodriguez', 'miguel.rodriguez@university.edu', '+63 918 765 4321', 'Bachelor of Science in Mathematics', '2nd Year'),
('2024-006', 'Carmen', 'Lopez', 'carmen.lopez@university.edu', '+63 913 654 3210', 'Bachelor of Science in Biology', '3rd Year'),
('2024-007', 'Pedro', 'Martinez', 'pedro.martinez@university.edu', '+63 919 543 2109', 'Bachelor of Business Administration', '1st Year'),
('2024-008', 'Rosa', 'Fernandez', 'rosa.fernandez@university.edu', '+63 916 432 1098', 'Bachelor of Science in Nursing', '4th Year'),
('2024-009', 'Carlos', 'Gonzalez', 'carlos.gonzalez@university.edu', '+63 921 321 0987', 'Bachelor of Science in Architecture', '2nd Year'),
('2024-010', 'Sofia', 'Herrera', 'sofia.herrera@university.edu', '+63 922 210 9876', 'Bachelor of Fine Arts', '3rd Year');

-- Display table structure
DESCRIBE students;

-- Display sample data
SELECT * FROM students ORDER BY created_at DESC;

-- Show table statistics
SELECT 
    COUNT(*) as total_students,
    COUNT(DISTINCT course) as total_courses,
    COUNT(CASE WHEN year_level = '1st Year' THEN 1 END) as first_year,
    COUNT(CASE WHEN year_level = '2nd Year' THEN 1 END) as second_year,
    COUNT(CASE WHEN year_level = '3rd Year' THEN 1 END) as third_year,
    COUNT(CASE WHEN year_level = '4th Year' THEN 1 END) as fourth_year
FROM students;