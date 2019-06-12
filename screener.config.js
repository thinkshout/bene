require('dotenv').load();
module.exports = {
  // full repository name for your project:
  projectRepo: 'thinkshout/' + process.env.TS_PROJECT,

  // this example assumes Environment Variables listed below exist on your system:
  apiKey: process.env.SCREENER_API_KEY,

  baseBranch: (process.env.CIRCLE_BRANCH === 'develop' || process.env.CIRCLE_BRANCH === 'master') ? 'master' : 'develop',
  failureExitCode: 0,

  // array of UI states to capture visual snapshots of.
  // each state consists of a url and a name.
  states: [
    {
      url: 'https://vr-dev-' + process.env.TERMINUS_SITE + '.pantheonsite.io',
      name: 'homepage'
    },
  ]
};