module.exports = {
  root: true,
  extends: [
    'eslint:recommended',
    'plugin:vue/vue3-recommended',
    'plugin:@typescript-eslint/recommended'
  ],
  plugins: ['vue', '@typescript-eslint', 'unused-imports'],
  parser: 'vue-eslint-parser',
  parserOptions: {
    parser: '@typescript-eslint/parser',
    ecmaVersion: 2020,
    sourceType: 'module'
  },
  rules: {
    // This rule will remove unused imports automatically
    'unused-imports/no-unused-imports': 'error',
    // Turn off the base rule as it can report incorrect errors
    '@typescript-eslint/no-unused-vars': 'off',
    // Use the unused-imports plugin's rule instead
    'unused-imports/no-unused-vars': [
      'warn',
      { 
        vars: 'all', 
        varsIgnorePattern: '^_', 
        args: 'after-used', 
        argsIgnorePattern: '^_' 
      }
    ]
  }
}

