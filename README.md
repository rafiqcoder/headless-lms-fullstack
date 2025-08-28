# Headless LMS with WooCommerce Integration

A modern, headless Learning Management System (LMS) built with **Next.js 15** frontend and **WordPress** backend, featuring full **WooCommerce** integration for paid courses.

## 🚀 Features

### Core LMS Features
- ✅ **Headless Architecture** - WordPress backend + Next.js frontend
- ✅ **GraphQL API** - Powered by WPGraphQL
- ✅ **Course Management** - Create, edit, and organize courses
- ✅ **User Enrollment** - Free and paid course enrollment
- ✅ **Responsive Design** - Built with Tailwind CSS
- ✅ **TypeScript** - Full type safety
- ✅ **Redux Toolkit** - State management

### WooCommerce Integration
- ✅ **Paid Courses** - Sell courses through WooCommerce
- ✅ **Automatic Enrollment** - Users enrolled after successful payment
- ✅ **WooCommerce Hooks** - Real-time enrollment on order completion
- ✅ **Custom Product Types** - Course-specific WooCommerce products
- ✅ **Seamless Checkout** - Direct redirect to WooCommerce checkout
- ✅ **Return URL Handling** - Post-purchase redirect back to course

## 🏗️ Architecture

```
├── headless-lms/                 # Next.js 15 Frontend
│   ├── src/
│   │   ├── app/                  # App Router
│   │   │   ├── api/             # API Routes
│   │   │   ├── courses/         # Course Pages
│   │   │   └── components/      # React Components
│   │   ├── hooks/               # Custom React Hooks
│   │   └── store/               # Redux Store
│   └── package.json
│
├── wp-content/
│   ├── plugins/
│   │   ├── lms-woocommerce-integration.php    # Custom WooCommerce Plugin
│   │   ├── wpgraphql-lms-extension.php        # GraphQL Extensions
│   │   └── headless-lms/                      # Core LMS Plugin
│   └── themes/headless-lms/                   # Minimal WordPress Theme
│
└── .gitignore                    # Excludes WordPress core & build files
```

## 🛠️ Technology Stack

### Frontend
- **Next.js 15** - React framework with App Router
- **TypeScript** - Type safety
- **Tailwind CSS** - Utility-first CSS
- **Redux Toolkit** - State management
- **React Hook Form** - Form handling

### Backend
- **WordPress** - Content management
- **WPGraphQL** - GraphQL API
- **WooCommerce** - E-commerce functionality
- **Custom Plugins** - LMS and integration logic

## 📦 Installation

### Prerequisites
- **XAMPP** (Apache, MySQL, PHP 8.0+)
- **Node.js 18+**
- **Composer** (for WordPress dependencies)

### 1. Clone Repository
```bash
git clone https://github.com/rafiqcoder/headless-lms-fullstack.git
cd headless-lms-fullstack
```

### 2. WordPress Setup
```bash
# Set up WordPress database
# Import your database or run WordPress installation

# Install WooCommerce plugin
# Activate custom LMS plugins:
# - lms-woocommerce-integration.php
# - wpgraphql-lms-extension.php
```

### 3. Next.js Setup
```bash
cd headless-lms
npm install
```

### 4. Environment Configuration
```bash
# Copy environment template
cp .env.example .env.local

# Configure your environment variables
WORDPRESS_API_URL=http://localhost/your-wordpress-site
NEXT_PUBLIC_WORDPRESS_URL=http://localhost/your-wordpress-site
```

### 5. Start Development
```bash
# Start Next.js development server
cd headless-lms
npm run dev

# WordPress should be running on XAMPP
# Visit: http://localhost:3000 (Next.js)
# Visit: http://localhost/your-wp-site (WordPress admin)
```

## 🎯 Key Features Demo

### Free Course Enrollment
1. Visit any free course page
2. Click "Enroll Now"
3. Immediate enrollment without payment

### Paid Course Purchase
1. Visit a paid course (e.g., Course 13 - $49.99)
2. Click "Enroll Now"
3. Redirected to WooCommerce checkout
4. Complete payment
5. Automatic enrollment via WooCommerce hooks
6. Access course content immediately

## 📁 Key Files

### WordPress Plugins
- `wp-content/plugins/lms-woocommerce-integration.php` - WooCommerce integration
- `wp-content/plugins/wpgraphql-lms-extension.php` - GraphQL extensions

### Next.js API Routes
- `src/app/api/courses/[id]/enrollment/route.ts` - Enrollment status
- `src/app/api/courses/[id]/woocommerce/route.ts` - WooCommerce integration
- `src/app/api/courses/[id]/enroll/route.ts` - Free course enrollment

### Frontend Components
- `src/app/courses/[id]/page.tsx` - Dynamic course details page
- `src/app/courses/page.tsx` - Course listing
- `src/hooks/useWooCommerce.ts` - WooCommerce state management

## 🔧 API Endpoints

### WordPress REST API
```
GET  /wp-json/lms/v1/course/{id}/product           # Get course product
POST /wp-json/lms/v1/course/{id}/checkout          # Generate checkout URL
GET  /wp-json/lms/v1/user/{id}/enrollments         # User enrollments
```

### Next.js API
```
GET  /api/courses/{id}/enrollment?userId={id}      # Check enrollment
GET  /api/courses/{id}/woocommerce                 # Get product info
POST /api/courses/{id}/woocommerce                 # Create checkout URL
POST /api/courses/{id}/enroll                      # Free enrollment
```

## 🧪 Testing

Visit the test page at `/test` to run comprehensive API tests:
- Enrollment status checking
- WooCommerce product integration
- Checkout URL generation
- Free course enrollment

## 🚀 Deployment

### Production Checklist
- [ ] Configure production WordPress database
- [ ] Set up SSL certificates
- [ ] Configure WooCommerce payment gateways
- [ ] Set production environment variables
- [ ] Build Next.js for production: `npm run build`
- [ ] Configure server redirects for proper routing

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -m 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support, please open an issue in the GitHub repository or contact the development team.

---

**Built with ❤️ by the Headless LMS Team**
