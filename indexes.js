db.getCollection('users').createIndex(
  { email: 1 },
  { unique: true, name: 'users_email_unique' }
);


db.getCollection('personal_access_tokens').createIndex(
  { tokenable_type: 1, tokenable_id: 1 },
  { name: 'tokenable_index' }
);


db.getCollection('tasks').createIndex({ title: 1 }, { name: 'tasks_title_index' });

print('MongoDB indexes created successfully.');
