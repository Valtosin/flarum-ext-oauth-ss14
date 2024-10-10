import app from 'flarum/admin/app';
import { ConfigureWithOAuthPage } from '@fof-oauth';

app.initializers.add('valtos-ss14', () => {
  app.extensionData
    .for('valtos-ss14')
    .registerPage(ConfigureWithOAuthPage);
});
