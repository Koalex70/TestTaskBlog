# Test Assignment: "Blog" Website in Pure PHP

## 1) Task Statement

Test assignment: build a simple but fully functional "Blog" website **without using any frameworks**.

## 2) Assignment Requirements

### Technology Stack

- PHP 8.1+
- MySQL
- Smarty template engine
- Frameworks are not allowed

### Data Structure

#### Category
- Name
- Description

#### Article
- Image
- Title
- Description
- Content
- Category (one or multiple)
- View count

### Required Pages

#### Home Page
- Display each category that contains articles
- For each such category, display 3 latest posts (by publication date)
- Display an "All Articles" button for each category

#### Category Page
- Display category title
- Display category description
- Display list of category articles
- Implement article sorting:
  - by view count
  - by publication date
- Implement pagination

#### Article Page
- Display full article information
- Display a block with 3 related articles

### Additional Functionality

- Implement seeding for categories and articles

### Evaluation Criteria

- Simplicity, readability, and code structure
- Project structure
- MySQL usage
- Level of independent implementation
- Depth of understanding of the solution