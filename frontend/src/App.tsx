import React from 'react';
import {Navbar} from "./components/Navbar";
import {Page} from "./components/Page";
import {Box} from "@material-ui/core";

const App = () => {
  return (
      <>
        <Navbar/>
          <Box paddingTop={'80px'}>
              <Page title="Categorias">
                  <h1>Categorias</h1>
              </Page>
          </Box>
      </>
  );
}

export default App;
