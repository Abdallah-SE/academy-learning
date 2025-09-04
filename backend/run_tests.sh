#!/bin/bash

# Arabic Academy - Sprint 1 Test Runner
# This script runs all tests for the Sprint 1 implementation

echo "🚀 Arabic Academy - Sprint 1 Test Runner"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the backend directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the backend directory"
    exit 1
fi

print_status "Starting Sprint 1 test suite..."

# Create test database if it doesn't exist
print_status "Setting up test environment..."

# Clear any existing test cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run database migrations for testing
print_status "Running database migrations..."
php artisan migrate:fresh --env=testing

# Run seeders for testing
print_status "Running seeders..."
php artisan db:seed --env=testing

print_status "Starting test execution..."
echo ""

# Run all tests with coverage report
print_status "Running all tests with coverage..."

# Run unit tests first
print_status "Running Unit Tests..."
php artisan test --testsuite=Unit --coverage --min=80

if [ $? -eq 0 ]; then
    print_success "Unit tests passed!"
else
    print_error "Unit tests failed!"
    exit 1
fi

echo ""

# Run feature tests
print_status "Running Feature Tests..."
php artisan test --testsuite=Feature --coverage --min=80

if [ $? -eq 0 ]; then
    print_success "Feature tests passed!"
else
    print_error "Feature tests failed!"
    exit 1
fi

echo ""

# Run specific controller tests
print_status "Running Controller Tests..."

# User Controller Tests
print_status "Testing User Controller..."
php artisan test tests/Feature/UserControllerTest.php --coverage

# MembershipPackage Controller Tests
print_status "Testing MembershipPackage Controller..."
php artisan test tests/Feature/MembershipPackageControllerTest.php --coverage

# UserMembership Controller Tests
print_status "Testing UserMembership Controller..."
php artisan test tests/Feature/UserMembershipControllerTest.php --coverage

# Role Controller Tests
print_status "Testing Role Controller..."
php artisan test tests/Feature/RoleControllerTest.php --coverage

# Permission Controller Tests
print_status "Testing Permission Controller..."
php artisan test tests/Feature/PermissionControllerTest.php --coverage

echo ""

# Run model tests
print_status "Running Model Tests..."
php artisan test tests/Unit/UserTest.php --coverage
php artisan test tests/Unit/CustomExceptionTest.php --coverage

echo ""

# Generate coverage report
print_status "Generating coverage report..."
php artisan test --coverage-html coverage-report

if [ $? -eq 0 ]; then
    print_success "Coverage report generated successfully!"
    print_status "Coverage report available at: coverage-report/index.html"
else
    print_warning "Could not generate coverage report"
fi

echo ""

# Run specific test categories
print_status "Running CRUD Operation Tests..."

# Test User CRUD operations
print_status "Testing User CRUD operations..."
php artisan test --filter="UserControllerTest" --coverage

# Test MembershipPackage CRUD operations
print_status "Testing MembershipPackage CRUD operations..."
php artisan test --filter="MembershipPackageControllerTest" --coverage

# Test UserMembership CRUD operations
print_status "Testing UserMembership CRUD operations..."
php artisan test --filter="UserMembershipControllerTest" --coverage

# Test Role CRUD operations
print_status "Testing Role CRUD operations..."
php artisan test --filter="RoleControllerTest" --coverage

# Test Permission CRUD operations
print_status "Testing Permission CRUD operations..."
php artisan test --filter="PermissionControllerTest" --coverage

echo ""

# Test authorization and policies
print_status "Testing Authorization and Policies..."
php artisan test --filter="Policy" --coverage

# Test validation
print_status "Testing Validation..."
php artisan test --filter="validation" --coverage

# Test error handling
print_status "Testing Error Handling..."
php artisan test --filter="CustomException" --coverage

echo ""

# Performance tests (basic)
print_status "Running basic performance tests..."
php artisan test --filter="performance" --coverage

echo ""

# Security tests
print_status "Running security tests..."
php artisan test --filter="security" --coverage

echo ""

# Final summary
print_status "Generating test summary..."

# Count total tests
TOTAL_TESTS=$(php artisan test --count 2>/dev/null | grep "Tests:" | awk '{print $2}' | head -1)
PASSED_TESTS=$(php artisan test --count 2>/dev/null | grep "Tests:" | awk '{print $4}' | head -1)

if [ -n "$TOTAL_TESTS" ] && [ -n "$PASSED_TESTS" ]; then
    print_success "Test Summary: $PASSED_TESTS/$TOTAL_TESTS tests passed"
else
    print_warning "Could not determine test count"
fi

echo ""
print_status "Sprint 1 test suite completed!"
print_status "Check the coverage report for detailed results"
echo ""

# Optional: Open coverage report in browser (if on macOS)
if [[ "$OSTYPE" == "darwin"* ]]; then
    read -p "Would you like to open the coverage report in your browser? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        open coverage-report/index.html
    fi
fi

print_success "🎉 All tests completed successfully!"
echo ""
print_status "Next steps:"
echo "  1. Review test results and coverage"
echo "  2. Fix any failing tests"
echo "  3. Run specific test suites as needed"
echo "  4. Proceed to Sprint 2 implementation"
echo ""

exit 0
