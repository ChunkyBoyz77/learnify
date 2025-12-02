# Learnify Database Schema

This document describes all database tables for the Learnify system.

## Table Overview

### Core Tables

#### 1. **users**
User accounts for learners and instructors.

**Fields:**
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `email_verified_at` - Email verification timestamp
- `password` - Hashed password
- `role` - User role (learner/instructor)
- `phone` - Phone number (nullable)
- `address` - Address (nullable)
- `bio` - Biography/description (nullable)
- `avatar` - Profile picture path (nullable)
- `date_of_birth` - Date of birth (nullable)
- `remember_token` - Remember me token
- `created_at`, `updated_at` - Timestamps

---

#### 2. **courses**
Course information created by instructors.

**Fields:**
- `id` - Primary key
- `instructor_id` - Foreign key to users (instructor)
- `title` - Course title
- `description` - Course description
- `price` - Course price (decimal)
- `image` - Course image path
- `slug` - URL-friendly identifier (unique)
- `is_active` - Whether course is active
- `is_published` - Whether course is published
- `enrollment_count` - Number of enrolled students
- `created_at`, `updated_at` - Timestamps

**Relationships:**
- Belongs to: User (instructor)
- Has many: Enrollments, Payments, Lessons, Quizzes, Assignments, Feedback

---

#### 3. **enrollments**
Student course enrollments.

**Fields:**
- `id` - Primary key
- `user_id` - Foreign key to users (student)
- `course_id` - Foreign key to courses
- `status` - Enum: pending, active, completed, cancelled
- `enrolled_at` - Enrollment timestamp
- `completed_at` - Course completion timestamp
- `created_at`, `updated_at` - Timestamps

**Unique Constraint:** `(user_id, course_id)`

**Relationships:**
- Belongs to: User, Course
- Has many: Payments

---

#### 4. **payments**
Payment transactions for course enrollments.

**Fields:**
- `id` - Primary key
- `user_id` - Foreign key to users (student)
- `enrollment_id` - Foreign key to enrollments (nullable)
- `course_id` - Foreign key to courses
- `payment_method_id` - Foreign key to payment_methods
- `transaction_id` - Unique transaction identifier
- `amount` - Payment amount (decimal)
- `status` - Enum: pending, processing, completed, failed, cancelled, refunded
- `payment_type` - Enum: enrollment, subscription, other
- `payment_details` - JSON field for payment gateway response
- `notes` - Additional notes
- `paid_at` - Payment completion timestamp
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `(user_id, status)`
- `(course_id, status)`
- `transaction_id`

**Relationships:**
- Belongs to: User, Enrollment, Course, PaymentMethod

---

#### 5. **payment_methods**
Available payment methods.

**Fields:**
- `id` - Primary key
- `name` - Payment method name (e.g., "Credit Card")
- `code` - Unique code (e.g., "credit_card")
- `description` - Description
- `is_active` - Whether method is active
- `config` - JSON configuration for payment gateway
- `created_at`, `updated_at` - Timestamps

**Relationships:**
- Has many: Payments

---

### Course Content Tables

#### 6. **lessons**
Course lessons/content.

**Fields:**
- `id` - Primary key
- `course_id` - Foreign key to courses
- `title` - Lesson title
- `description` - Lesson description
- `content` - Lesson content/notes
- `video_url` - Video link (nullable)
- `order` - Display order within course
- `duration_minutes` - Lesson duration
- `is_published` - Whether lesson is published
- `created_at`, `updated_at` - Timestamps

**Index:** `(course_id, order)`

**Relationships:**
- Belongs to: Course
- Has many: CourseMaterials, Quizzes, Assignments, Feedback

---

#### 7. **course_materials**
Files, videos, and other materials for courses/lessons.

**Fields:**
- `id` - Primary key
- `course_id` - Foreign key to courses (nullable)
- `lesson_id` - Foreign key to lessons (nullable)
- `title` - Material title
- `description` - Material description
- `type` - Enum: file, video, link, document, other
- `file_path` - Path to uploaded file
- `file_name` - Original file name
- `file_size` - File size in bytes
- `mime_type` - File MIME type
- `external_url` - External link URL
- `order` - Display order
- `is_downloadable` - Whether file can be downloaded
- `created_at`, `updated_at` - Timestamps

**Index:** `(course_id, lesson_id)`

**Relationships:**
- Belongs to: Course, Lesson

---

### Assessment Tables

#### 8. **quizzes**
Quizzes for courses/lessons.

**Fields:**
- `id` - Primary key
- `course_id` - Foreign key to courses
- `lesson_id` - Foreign key to lessons (nullable)
- `title` - Quiz title
- `description` - Quiz description
- `instructions` - Quiz instructions
- `time_limit_minutes` - Time limit for quiz
- `total_questions` - Total number of questions
- `passing_score` - Percentage required to pass
- `max_attempts` - Maximum attempts allowed
- `is_published` - Whether quiz is published
- `available_from` - Start availability date
- `available_until` - End availability date
- `created_at`, `updated_at` - Timestamps

**Index:** `(course_id, lesson_id)`

**Relationships:**
- Belongs to: Course, Lesson
- Has many: QuizQuestions, QuizSubmissions

---

#### 9. **quiz_questions**
Questions for quizzes.

**Fields:**
- `id` - Primary key
- `quiz_id` - Foreign key to quizzes
- `question` - Question text
- `type` - Enum: multiple_choice, true_false, short_answer, essay
- `options` - JSON array of options (for multiple choice)
- `correct_answer` - JSON correct answer(s)
- `explanation` - Explanation shown after answering
- `points` - Points for this question
- `order` - Display order
- `created_at`, `updated_at` - Timestamps

**Index:** `(quiz_id, order)`

**Relationships:**
- Belongs to: Quiz

---

#### 10. **quiz_submissions**
Student quiz submissions.

**Fields:**
- `id` - Primary key
- `user_id` - Foreign key to users (student)
- `quiz_id` - Foreign key to quizzes
- `course_id` - Foreign key to courses
- `answers` - JSON object of answers: {question_id: answer}
- `score` - Total score
- `percentage` - Score percentage
- `is_passed` - Whether quiz was passed
- `attempt_number` - Attempt number
- `started_at` - Quiz start timestamp
- `submitted_at` - Submission timestamp
- `time_taken_seconds` - Time taken in seconds
- `status` - Enum: in_progress, submitted, graded
- `feedback` - Instructor feedback
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `(user_id, quiz_id)`
- `(quiz_id, user_id, attempt_number)` - Named: `quiz_user_attempt_idx`

**Relationships:**
- Belongs to: User, Quiz, Course

---

#### 11. **assignments**
Assignments for courses/lessons.

**Fields:**
- `id` - Primary key
- `course_id` - Foreign key to courses
- `lesson_id` - Foreign key to lessons (nullable)
- `title` - Assignment title
- `description` - Assignment description
- `instructions` - Assignment instructions
- `total_points` - Total points possible
- `due_date` - Due date timestamp
- `allows_late_submission` - Whether late submissions are allowed
- `max_submissions` - Maximum submissions allowed
- `submission_type` - Enum: file, text, both
- `allowed_file_types` - JSON array of allowed file types
- `max_file_size_mb` - Maximum file size in MB
- `is_published` - Whether assignment is published
- `available_from` - Start availability date
- `available_until` - End availability date
- `created_at`, `updated_at` - Timestamps

**Index:** `(course_id, lesson_id)`

**Relationships:**
- Belongs to: Course, Lesson
- Has many: AssignmentSubmissions

---

#### 12. **assignment_submissions**
Student assignment submissions.

**Fields:**
- `id` - Primary key
- `user_id` - Foreign key to users (student)
- `assignment_id` - Foreign key to assignments
- `course_id` - Foreign key to courses
- `submission_text` - Text submission content
- `submitted_files` - JSON array of file paths
- `score` - Assigned score
- `percentage` - Score percentage
- `submission_number` - Submission attempt number
- `status` - Enum: submitted, graded, returned, resubmitted
- `feedback` - Instructor feedback
- `instructor_notes` - Private instructor notes
- `is_late` - Whether submission was late
- `submitted_at` - Submission timestamp
- `graded_at` - Grading timestamp
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `(user_id, assignment_id)`
- `(assignment_id, user_id, submission_number)` - Named: `asgn_sub_user_subnum_idx`

**Relationships:**
- Belongs to: User, Assignment, Course

---

### Feedback Table

#### 13. **feedback**
Student feedback on lessons, instructors, or courses.

**Fields:**
- `id` - Primary key
- `user_id` - Foreign key to users (student who gave feedback)
- `course_id` - Foreign key to courses
- `lesson_id` - Foreign key to lessons (nullable)
- `instructor_id` - Foreign key to users (instructor being reviewed)
- `comment` - Feedback comment/suggestion
- `rating` - Rating 1-5 (nullable)
- `feedback_type` - Enum: lesson, instructor, course, general
- `status` - Enum: pending, approved, rejected
- `instructor_response` - Instructor's response to feedback
- `is_anonymous` - Whether feedback is anonymous
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `(course_id, user_id)`
- `(instructor_id, status)`
- `(lesson_id)`

**Relationships:**
- Belongs to: User (student), Course, Lesson, User (instructor)

---

## Database Relationships Summary

### User (Instructor) → Courses
- One instructor can create many courses
- Course has one instructor

### User (Student) → Enrollments
- One student can have many enrollments
- Enrollment belongs to one student

### Course → Enrollments
- One course can have many enrollments
- Enrollment belongs to one course

### Course → Lessons
- One course can have many lessons
- Lesson belongs to one course

### Lesson → Course Materials
- One lesson can have many materials
- Material belongs to one lesson (or course)

### Course → Quizzes
- One course can have many quizzes
- Quiz belongs to one course

### Quiz → Quiz Questions
- One quiz can have many questions
- Question belongs to one quiz

### User (Student) → Quiz Submissions
- One student can submit many quizzes
- Submission belongs to one student and one quiz

### Course → Assignments
- One course can have many assignments
- Assignment belongs to one course

### User (Student) → Assignment Submissions
- One student can submit many assignments
- Submission belongs to one student and one assignment

### User (Student) → Feedback
- One student can give many feedback entries
- Feedback belongs to one student

### Course → Feedback
- One course can receive many feedback entries
- Feedback belongs to one course

---

## Notes

- All tables include `created_at` and `updated_at` timestamps
- Foreign keys use `onDelete('cascade')` to maintain referential integrity
- JSON fields are used for flexible data storage (options, answers, files, etc.)
- Indexes are added for common query patterns
- Enum fields provide data validation at the database level

