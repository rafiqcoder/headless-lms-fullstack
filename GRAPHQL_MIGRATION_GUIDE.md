# GraphQL Migration - Setup Steps

## 🚨 **Issue Fixed: REST API → GraphQL Migration**

The error `GET http://localhost:3000/api/courses?featured=true 500` was caused by the API route still trying to use old REST API methods while we've migrated to GraphQL.

## ✅ **What I've Fixed:**

### 1. **Updated API Route (`/api/courses`)**

- ❌ **Before:** Used `courseApi.getFeaturedCourses()` (REST)
- ✅ **After:** Uses GraphQL queries with Apollo Client

### 2. **Added GraphQL Proxy Route (`/api/graphql`)**

- Provides server-side GraphQL proxy to avoid CORS issues
- Handles authentication and error responses

### 3. **Enhanced WPGraphQL LMS Extension**

- Added `updateLessonProgress` mutation
- Better error handling and response formatting

## 🔧 **Required Setup Steps:**

### **Step 1: Activate WordPress Plugins**

1. Go to `http://localhost/lms/wp-admin/plugins.php`
2. **Activate:**
   - ✅ WPGraphQL
   - ✅ WPGraphQL LMS Extension
   - ✅ Headless LMS Performance Optimizer

### **Step 2: Create Test Content**

1. Go to `http://localhost/lms/wp-admin/edit.php?post_type=course`
2. **Create a few test courses:**

   ```
   Title: Introduction to Web Development
   Content: Learn HTML, CSS, and JavaScript fundamentals

   Custom Fields:
   _price: 99.00
   _currency: BDT
   _level: beginner
   _duration: 20
   _instructor: 1
   ```

### **Step 3: Test GraphQL API**

```bash
# Test endpoint
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"query":"{ courses { nodes { id title price } } }"}' \
  http://localhost/lms/graphql
```

### **Step 4: Test Frontend**

1. Start Next.js development server:

   ```bash
   cd c:\xampp\htdocs\lms\headless-lms
   npm run dev
   ```

2. **Test API endpoints:**
   - `http://localhost:3000/api/courses` (now uses GraphQL)
   - `http://localhost:3000/api/courses?featured=true`
   - `http://localhost:3000/api/graphql` (GraphQL proxy)

### **Step 5: Add Test Component**

Add to any page to test GraphQL:

```tsx
import GraphQLTest from "@/components/GraphQLTest";

export default function TestPage() {
  return <GraphQLTest />;
}
```

## 📊 **Expected Results:**

### **Working API Responses:**

```json
// GET /api/courses?featured=true
{
  "courses": [
    {
      "id": "1",
      "title": "Introduction to Web Development",
      "price": 99,
      "currency": "BDT",
      "level": "beginner",
      "instructor": {
        "name": "John Doe"
      }
    }
  ],
  "pagination": {
    "currentPage": 1,
    "hasNextPage": false
  }
}
```

### **GraphQL Direct Response:**

```json
// POST /graphql
{
  "data": {
    "courses": {
      "nodes": [
        {
          "id": "1",
          "title": "Introduction to Web Development",
          "price": 99
        }
      ]
    }
  }
}
```

## 🔍 **Troubleshooting:**

### **If you get "No courses found":**

1. **Check plugin activation** in WordPress admin
2. **Create courses** with proper custom fields
3. **Verify GraphQL schema** at `http://localhost/lms/graphql` (GraphiQL IDE)

### **If you get CORS errors:**

1. **Use the proxy route:** `/api/graphql` instead of direct WordPress endpoint
2. **Check .htaccess** file has CORS headers

### **If you get 500 errors:**

1. **Check WordPress error logs** in `wp-content/debug.log`
2. **Verify database tables** were created (lms_enrollments, lms_progress)
3. **Test GraphQL endpoint directly** with curl

## 🚀 **Benefits of This Migration:**

- ✅ **Single Request** instead of multiple REST calls
- ✅ **Type Safety** with TypeScript interfaces
- ✅ **Better Caching** with Apollo Client
- ✅ **Real-time Updates** and optimistic UI
- ✅ **Reduced Bandwidth** (only fetch needed data)

Your LMS is now fully powered by GraphQL! 🎉
