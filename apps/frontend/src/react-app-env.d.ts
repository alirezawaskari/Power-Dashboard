interface ImportMetaEnv {
  readonly BASE_URL: string;
  readonly REACT_APP_API_URL: string;
  readonly GENERATE_SOURCEMAP: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
